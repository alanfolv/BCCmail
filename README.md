# BCCmail
**Disciplina:** Desenvolvimento Web I
**Tema:** Clone do Gmail (Webmail)  
**Arquitetura Base:** HTML5, CSS3/Bootstrap, JavaScript Vanilla (ES6), PHP Procedural/Modular com PDO + MariaDB
**Integrantes:** Gabriel Barreto, Bruno Reis, Alan Oliveira

## Sobre o Projeto:
- O BCCmail é um projeto acadêmico desenvolvido como parte da disciplina de Desenvolvimento Web I. A proposta consiste na criação de um sistema de webmail inspirado no Gmail, reproduzindo algumas de suas principais características de navegação, organização e gerenciamento de mensagens.
- O projeto tem como finalidade aplicar os conhecimentos de desenvolvimento web, integração entre front-end e back-end, manipulação de banco de dados e validação de formulários.


## Responsabilidade de Integrantes
1. Integrante A - *Alan*
    - Interface, layout e identidade visual.

2. Integrante B - *Gabriel*
   - Back-end, banco de dados e autenticação.

3. Integrante C - *Bruno*
   - Formulários, validações e regras de negócio.


## Funcionalidades Principais do BBCmail
- Interface intuitiva, com navegação simples e organizada.
- Autenticação de usuários por e-mail e senha.
- Visualização e organização de mensagens recebidas.
- Envio de e-mails para usuários cadastrados.
- Gerenciamento de mensagens enviadas.
- Marcação de mensagens como importantes.
- Armazenamento e recuperação de rascunhos.
- Gerenciamento de mensagens na Lixeira.


## Regras de Negócio:
**Regra 1: Armazenamento em Rascunho Automático e Validação de Destinatário Existente:** 
Antes de realizar o envio de uma mensagem, o sistema deverá verificar se o endereço de e-mail informado está em um formato válido e se pertence a uma conta cadastrada. Caso o destinatário seja inválido ou não esteja registrado no domínio, o envio será interrompido
e a mensagem será armazenada. O usuário receberá uma notificação do salvamento do e-mail. Salvamento administrado em *Rascunho*.

**Regra 2: Cota de Armazenamento por Usuário (Simulação de Espaço Limite):** 
Para simular uma limitação de espaço, cada usuário terá um limite de, por exemplo, no máximo 50 mensagens armazenadas no sistema.
Quando esse limite for atingido, novos envios serão bloqueados até que o usuário libere espaço no sistema. Quando o limite estiver quase cheio deverá ser informado ao usuário por meio de caixas de mensagens fixas na tela. Exclusão administrada em *Lixeira*.