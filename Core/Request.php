<?php


namespace Core;


class Request
{
    private $get;
    private $post;
    private $files;
    private $server;

    public function __construct($get, $post, $files, $server)
    {
        $this->get = $get;
        $this->post = $post;
        $this->files = $files;
        $this->server = $server;
    }

    /**
     * détection des requêtes AJAX
     */
    public function isAjaxRequest()
    {
        return (!empty($this->server['HTTP_X_REQUESTED_WITH']) && strtolower($this->server['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
    }

    /**
     * @param $key la clé à chercher dans GET
     * @param $default la valeur à renvoyer si $key n'existe pas
     * @return null
     */
    public function getGetParam($key, $default = null)
    {
        if (!isset($this->get[$key])) {
            return $default;
        }
        return $this->get[$key];
    }

    /**
     * @param $key la clé à chercher dans POST
     * @param $default la valeur à renvoyer si $key n'existe pas
     * @return null
     */
    public function getPostParam($key, $default)
    {
        if (!isset($this->post[$key])) {
            return $default;
        }
        return $this->post[$key];
    }

    /**
     * obtenir tous les paramètres POST
     * @return array
     */
    public function getAllPostParams()
    {
        return $this->post;
    }
}