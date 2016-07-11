<?php

class SiteController extends WsController {

    /**
     * index
     *
    */
    public function index()
    {
        $this->title = ' - small and fast PHP framework';
        $this->render('index');
    }


    /**
     * introduction
     *
     */
    public function intro()
    {
        $this->title = ' - introduction';
        $this->render('intro');
    }


    /**
     * development guide
     *
     */
    public function guide()
    {
        $this->title = ' - basic tutorial';
        $this->render('guide');
    }


    /**
     * authentication guide
     *
     */
    public function auth_guide()
    {
        $this->title = ' - user authentication guide';
        $this->render('auth_guide');
    }


    /**
     * downloads
     *
     */
    public function downloads()
    {
        $this->title =' - downloads';
        $this->render('downloads');
    }
}
