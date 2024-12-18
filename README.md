# Guia para Uso do Container Docker para Projetos PHP

Este guia tem como objetivo auxiliar os desenvolvedores a configurarem e utilizarem um ambiente conteinerizado para os projetos PHP. Este ambiente replica o ambiente de QA atual, garantindo consistência e facilidade de setup, além de facilitar o gerenciamento e a integração contínua dos projetos.

## Pré-requisitos

As aplicações PHP da Nagem, para o ambiente de desenvolvimento, são dependentes do acesso à VPN, então antes de prosseguir verfique suas credenciais de acesso.

Certifique-se, também, de que você tem o Docker e o Docker Compose instalados em sua máquina. Siga as instruções abaixo para instalar essas ferramentas, caso ainda não as tenha.

**Instalando Docker**

Para instalar o Docker, siga as instruções específicas para o seu sistema operacional:


Docker: [Instalação do Docker:](https://docs.docker.com/get-docker/)
Docker Compose: [Instalação do Docker Compose:](https://docs.docker.com/compose/install/)

*Obs.:*

*O Docker Compose geralmente é instalado junto com o Docker Desktop no Windows. No Linux, você pode seguir as instruções em Instalação do Docker Compose.*

## Estrutura do Projeto

O projeto deve seguir a estrutura de pastas abaixo após o BASE\_DIR:

```bash
BASE_DIR
 ├── SistemasProducao
 │  └── Web
 │      ├── conagemweb
 │      ├── intranet
 │      ├── administracao
 │      ├── nol
 │      └── nagemcombr 
 │   └── vhosts.d
 │   └── log
 ├──  initdb
 │      └── create_db.sql 
 └── .env
 └── docker-compose.yml
```
*Obs.: `BASE_DIR` é o diretório de trabalho onde o desenvolvedor irá criar seu workspace*

*   **Web/**: Contém os arquivos do projeto PHP.
*   **vhosts.d/**: Contém os [arquivos](/Arquitetura-Cloud-NAGEM/Ambiente-Conteinerizado-para-Projetos-PHP/Guia-para-Uso-do-Container/BASE_DIR/vhosts.d) de configuração de virtual hosts do Apache.
*   **log/**: Contém os logs do Apache.
*   **initdb/**: Contém o [script SQL](https://dev.azure.com/TI-Desenvolvimento/Interliga%C3%A7%C3%A3o%20OCC%20-%20Grupo%20Mult/_wiki/wikis/Interliga%C3%A7%C3%A3o-OCC---Grupo-Mult.wiki/4099/create_db.sql) para criação dos bancos de sessões e suas tabelas.

## Arquivo `.env`

No terminal, configure a variável de ambiente `BASE_DIR` para apontar para o diretório raiz do projeto (`workspace`). Esta etapa é importante para garantir que os volumes mapeados no `docker-compose.yml` sejam configurados corretamente.
    
`export BASE_DIR=$(pwd)`

Para sistemas operacionais **windows** em `/caminho/para/BASE_DIR` crie um arquivo `.env` com o conteúdo:

```bash
BASE_DIR=/caminho/para/BASE_DIR
```

## Arquivo `docker-compose.yml`

O conteúdo do arquivo `docker-compose.yml` que deverá ser criado no diretório raiz do projeto (`BASE_DIR`) pode ser verificado nesse [link](/Arquitetura-Cloud-NAGEM/Ambiente-Conteinerizado-para-Projetos-PHP/Guia-para-Uso-do-Container/BASE_DIR/docker%2Dcompose.yml).

## Instruções para Uso

1. **Clone o Repositório**
Clone o repositório do projeto para o seu diretório de trabalho local:
    
```bash
svn checkout https://10.1.0.58/repos/web/conagemweb/trunk BASE_DIR/SistemasProducao/Web/conagemweb 
svn checkout https://10.1.0.58/repos/web/intranet/trunk BASE_DIR/SistemasProducao/Web/intranet 
svn checkout https://10.1.0.58/repos/web/nagemcombr/trunk BASE_DIR/SistemasProducao/Web/nagemcombr`
svn checkout https://10.1.0.58/repos/web/administracao/trunk BASE_DIR/SistemasProducao/Web/nagemcombr`
svn checkout https://10.1.0.58/repos/web/nol/trunk BASE_DIR/SistemasProducao/Web/nagemcombr`
```

*Obs.: uma opção é usar o [TottoiseSVN](https://tortoisesvn.net/downloads.html)*

Fazer checkout na pasta a seguir:

```bash
BASE_DIR
 ├── SistemasProducao
 │  └── Web
```
    
2.  **Navegue até o Diretório do Projeto**
    
    `cd /caminho/para/BASE_DIR`

3.  **Configuração do Hosts**

    Adicione as seguintes entradas ao arquivo hosts do sistema operacional:

    #### Linux

    Edite o arquivo `/etc/hosts`:

    ```nano /etc/hosts```

    Adicione as seguintes linhas:

    ```
    127.0.0.1 trunk.nagem.com.br
    127.0.0.1 trunk.conagemweb.com.br
    127.0.0.1 trunk.administracao.com.br
    127.0.0.1 trunk.nagemonline.com.br
    ```

    #### Windows

    Edite o arquivo `C:\Windows\System32\drivers\etc\hosts`:

    Adicione as seguintes linhas:

    ```
    127.0.0.1 trunk.nagem.com.br
    127.0.0.1 trunk.conagemweb.com.br
    127.0.0.1 trunk.administracao.com.br
    127.0.0.1 trunk.nagemonline.com.br
    ```

4. **Configuração dos Projetos**

    Para facilitar a configuração inicial dos projetos para o ambiente de desenvolvimento, esse guia disponibiliza os arquivos por projeto que devem ser modificados. Bastando somente alterar/incluir o conteúdo dos arquivos no repositório dos projetos, pelo conteúdo dos arquivos desse guia. Abaixo temos o links de cada projeto:

    #### SistemasProducao/Web/intranet

    #### SistemasProducao/Web/conagemweb

    #### SistemasProducao/Web/nagemcombr

    #### SistemasProducao/Web/administracao

    #### SistemasProducao/Web/nol
.

5. **Configuração dos Virtual Hosts**

    Inclua os arquivos de configuração da pasta `vhosts.d` fornecidos nesse [link](/Arquitetura-Cloud-NAGEM/Ambiente-Conteinerizado-para-Projetos-PHP/Guia-para-Uso-do-Container/BASE_DIR/vhosts.d).
    
6.  **Suba o Container**
    Execute o comando abaixo para subir o container com o Docker Compose:
    
    `docker-compose up -d`
    
    Este comando irá criar e iniciar o container em segundo plano.
    
7.  **Acesse a Aplicação**

    Após levantar o container, você poderá acessar a aplicação no navegador através do endereço:
    
    `http://trunk.nagem.com.br`
    `http://trunk.nagem.com.br/intranet`
    `http://trunk.conagemweb.com.br/`

8.  **Verificar logs da aplicações**

    Para verificar os logs das aplicações, no prompr de comando é só digitar a instrução abaixo:

```bash
#nol
docker exec -it nagem-php-container tail -f /var/log/apache2/nagemonline.localhost-access.log

#administração B2C
docker exec -it nagem-php-container tail -f /var/log/apache2/administracaob2c.localhost-error.log

#conagem
docker exec -it nagem-php-container tail -f /var/log/apache2/trunknagemcombr.localhost-error.log

#nagemcombr
docker exec -it nagem-php-container tail -f /var/log/apache2/conagemweb.localhost-error.log

```

## Problemas Comuns e Soluções

*   **Erro ao Montar Volumes**: Verifique se o caminho especificado na variável `BASE_DIR` está correto e se as permissões de leitura/escrita estão configuradas adequadamente.
*   **Conexão Recusada**: Certifique-se de que o container está rodando corretamente e que as portas estão mapeadas corretamente (verifique com `docker ps`).
*   **Erros no Apache**: Verifique os logs do Apache no diretório `log` para identificar e solucionar possíveis problemas.

## Conclusão

Seguindo este guia, os desenvolvedores poderão configurar e utilizar o ambiente conteinerizado para os projetos PHP de maneira eficiente e padronizada, replicando o ambiente de QA atual. Isso facilitará o setup inicial, a manutenção e a integração contínua dos projetos.
