<?php

namespace App\Controllers;

class Embed extends BaseController
{
    public function index()
    {
        if ($redir = $this->checkAuth()) return $redir;

        $data = [
            'title'      => 'โค้ดสำหรับติดตั้ง Chat Widget บนเว็บไซต์',
            'activeMenu' => 'embed',
            'chatHost'   => base_url(),
        ];

        return view('settings/widget_embed', array_merge($this->data, $data));
    }
}