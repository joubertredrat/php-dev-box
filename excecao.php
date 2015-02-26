<?php
/**
 * Classe de tratamento de exceção, versão adaptada para o framework CodeIgniter.
 *
 * @author Joubert Guimarães de Assis "RedRat" <joubert@redrat.com.br>
 * @copyright Copyright (c) 2013, RedRat Consultoria
 * @licenseGPL version 2
 */
class Excecao extends Exception {

    /**
     * Construtor da classe.
     *
     * @param string $msg Mensagem de erro.
     * @return void
     */
    public function __construct($msg) {
        if(defined('SISTEMA_ERRO_EMAIL') && Funcoes::validaProvedorEmail(SISTEMA_ERRO_EMAIL))
        {
            $cabecalho = 'From: '.SISTEMA_ERRO_EMAIL."\r\n".'X-Mailer: PHP/'.phpversion();
            $cabecalho .= 'MIME-Version: 1.0' . "\r\n";
            $cabecalho .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

            $msg .= "\r\n";
            $msg .= 'Dados do ambiente:'."\r\n<pre>";
            $msg .= '$_SERVER = '.var_export($_SERVER, true)."\r\n";
            $msg .= '$_POST = '.var_export($_POST, true)."\r\n";
            $msg .= '$_GET = '.var_export($_GET, true)."\r\n";
            $msg .= '$_REQUEST = '.var_export($_REQUEST, true)."\r\n";
            $msg .= '$_COOKIE = '.var_export($_COOKIE, true)."</pre>\r\n";
            
            mail(SISTEMA_ERRO_EMAIL, 'Exceção disparada em ' . time(), $msg, $cabecalho);
        }
        parent::__construct($msg);
    }

    /**
     * Retorna a mensagem de erro de acordo com o estado da aplicação.
     *
     * @return string Mensagem de erro.
     */
    public function getMessagePato() {
        switch (ENVIRONMENT) {
            case 'development':
                    return parent::getMessage();
                break;
            case 'testing':
            case 'production':
            default:
                return 'Ocorreu um erro na rotina e a equipe de suporte foi notificada.';
                break;
        }
    }
}
