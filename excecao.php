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
            mail(SISTEMA_ERRO_EMAIL, 'Exeção disparada em ' . time(), $msg);
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