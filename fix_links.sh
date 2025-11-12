#!/usr/bin/env bash
# fix_links.sh
# Script para rodar localmente e converter links que começam com "/" em links relativos,
# além de ajustar redirects header('Location: /...') para não apontarem para a raiz do servidor.
#
# Uso:
#   ./fix_links.sh           # dry-run: mostra diffs das mudanças propostas
#   ./fix_links.sh --apply   # aplica as mudanças (faz backup dos arquivos com sufixo .bak)
#   ./fix_links.sh --root path/to/repo --apply
#
# Atenção:
# - Cria backups (.bak) quando usado com --apply.
# - Revise os diffs antes de aplicar; nem todos os "/" raiz devem ser removidos (ex: URLs externas, //cdn...).
# - Teste em um branch/local antes de commitar.

set -euo pipefail

ROOT="."
APPLY=false
BACKUP_SUFFIX=".bak"
EXTENSIONS=(php html htm css js)

print_usage() {
  cat <<EOF
Usage: $0 [--root DIR] [--apply] [--backup-suffix SUFFIX] [--ext csv]
Options:
  --root DIR           Diretório raiz do projeto (default: .)
  --apply              Aplica as mudanças (por padrão faz dry-run e só mostra diffs)
  --backup-suffix S    Sufixo para arquivos de backup (default: .bak)
  --ext ext1,ext2,...  Extensões a serem verificadas (default: php,html,htm,css,js)
  -h, --help           Mostra esta ajuda
EOF
}

# parse args
while (( "$#" )); do
  case "$1" in
    --root)
      ROOT="$2"; shift 2;;
    --apply)
      APPLY=true; shift;;
    --backup-suffix)
      BACKUP_SUFFIX="$2"; shift 2;;
    --ext)
      IFS=',' read -r -a EXTENSIONS <<< "$2"; shift 2;;
    -h|--help)
      print_usage; exit 0;;
    *)
      echo "Unknown arg: $1"; print_usage; exit 1;;
  esac
done

# Build find expression
find_expr=()
for ext in "${EXTENSIONS[@]}"; do
  find_expr+=( -iname "*.${ext}" -o )
done
# remove trailing -o
unset 'find_expr[${#find_expr[@]}-1]'

echo "Root: $ROOT"
echo "Apply changes: $APPLY"
echo "Extensions: ${EXTENSIONS[*]}"
echo

# Perl script with safe regexes:
# - Remove initial slashes in href/src/action and similar attributes IF:
#   * The value does NOT start with: //, http:, https:, mailto:, # (anchors)
# - Adjust header('Location: /path') -> header('Location: path')
# - Skip protocol-relative URLs (//), absolute URLs (http(s)://), mailto:, and fragment-only (#)
PERL_EXPR='
  # 1) Attributes (href, src, action)
  s{(href|src|action)=([\"\'])/+(?!/|https?:|mailto:|#)([^\"\']+)\2}{$1=$2$3$2}g;

  # 2) <link ... href="/assets/..."> already handled by rule 1

  # 3) header("Location: /path") and header(\'Location: /path\')
  s{header\(\s*([\"\'])Location:\s*/+(?!/|https?:)([^\"\']+)\1\s*\)}{header($1Location: $2$1)}g;

  # 4) PHP redirects using double quotes with space variations
  s{header\(\s*([\"\'])Location:\s*/+(?!/|https?:)([^\"\']+)\1\s*\)\s*;}{header($1Location: $2$1); }g;
'

# Find files and process them
modified=0
changed_files=()

while IFS= read -r -d '' file; do
  # apply perl transform to the whole file
  tmp="$(mktemp)"
  perl -0777 -pe "$PERL_EXPR" "$file" > "$tmp"

  if ! cmp -s "$file" "$tmp"; then
    echo "----------------------------------------"
    echo "Proposed changes: $file"
    if [ "$APPLY" = false ]; then
      echo "Diff (preview):"
      diff -u --label "orig: $file" --label "new:  $file (proposed)" "$file" "$tmp" | sed 's/^/    /'
    else
      # create backup
      cp -a "$file" "$file${BACKUP_SUFFIX}"
      mv "$tmp" "$file"
      echo "Applied (backup at ${file}${BACKUP_SUFFIX})"
    fi
    modified=$((modified+1))
    changed_files+=("$file")
  else
    rm -f "$tmp"
  fi
done < <(find "$ROOT" -type f \( "${find_expr[@]}" \) -print0)

echo
if [ "$modified" -eq 0 ]; then
  echo "Nenhuma alteração proposta/encontrada."
else
  echo "Total de arquivos com mudanças propostas: $modified"
  if [ "$APPLY" = false ]; then
    echo "Execute com --apply para aplicar as mudanças (backups serão criados com sufixo $BACKUP_SUFFIX)."
  else
    echo "Mudanças aplicadas. Revise e rode testes. Backups foram criados com sufixo $BACKUP_SUFFIX."
  fi
fi

exit 0