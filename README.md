# TCC - Sistema de Escalação de Trabalhos Intermitentes

Tecnologias: PHP 8+, MySQL/MariaDB, MySQLi, HTML, CSS, JavaScript, Bootstrap 5.

## Funcionalidades

- Usuário (trabalhador)
  - Cadastro/Login
  - Perfil (ver/editar foto, telefone, email, senha; excluir conta)
  - Tela principal com cards dos jobs (eventos)
  - Pesquisa de jobs
  - Candidatar-se a jobs
  - Tela de empresas que oferecem filiação

- Empresa (contratante)
  - Cadastro/Login
  - Perfil (ver/editar foto, telefone, email, senha; excluir conta)
  - Dashboard com cards de eventos
  - Criar novos eventos (nome, descrição, data do evento, vagas, imagem, credenciamento/local)
  - Menu lateral: Eventos, Funcionários, Perfil, Credenciamentos
  - Tela de evento: lista candidatos com checkbox
    - Selecionar primeiros até limite de vagas
    - Remover tudo
    - Pesquisar por nomes
    - Favoritar funcionários (ficam primeiro)
    - Acessar perfil do funcionário
    - Exportar selecionados para Excel (CSV)
  - Funcionários: ver Favoritos e Selecionados (com Remover, Favoritar/Desfavoritar, Mensagem)

- Regra de CPF
  - O mesmo CPF não pode ser cadastrado como Usuário e Empresa ao mesmo tempo.
  - Implementado com a tabela `cpf_registry`. Se a empresa usar CNPJ (14 dígitos), não é registrado na `cpf_registry`. Se usar CPF (11 dígitos), o CPF é testado e registrado.

- Credenciamentos (locais)
  - Empresas podem cadastrar “Credenciamentos” (nome, endereço, cidade, UF) e associar aos eventos.

## Requisitos

- PHP 8+
- MySQL/MariaDB
- Composer não é necessário (sem libs externas)
- Extensão mysqli habilitada
- Pasta `uploads/` com permissão de escrita

## Passos para rodar

1) Crie um banco e importe o schema:
- Crie o banco, por exemplo `tcc_intermitente`
- Importe o arquivo `db.sql`

2) Configure a conexão no arquivo `includes/db.php`:
- Ajuste host, usuário, senha e nome do banco

3) Ajuste permissões de pastas:
- Crie as pastas: `uploads/events`, `uploads/users`, `uploads/companies`
- Dê permissão de escrita ao PHP (ex.: `chmod -R 775 uploads`)

4) Rode em servidor local:
- Ex.: `php -S localhost:8000` na raiz do projeto
- Acesse `http://localhost:8000`

Credenciais de teste:
- Você pode criar contas pela própria aplicação.

## Observações

- Exportação Excel é realizada em CSV com header apropriado (abre no Excel).
- Senhas com `password_hash()` e `password_verify()`.
- Todas as queries com prepared statements (MySQLi).
- Favoritos aparecem primeiro na lista de candidatos.
- Pesquisa por nome no evento e na listagem de jobs.
# trabalho-intermitente
