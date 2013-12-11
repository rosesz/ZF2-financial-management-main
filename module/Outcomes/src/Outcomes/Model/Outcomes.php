<?php

namespace Outcomes/Model;

class Outcomes
{
    public $id;
    public $amount;
    public $category;
    public $date;

    public function exchangeArray($data)
    {
         $this->id       = (!empty($data['id'])) ? $data['id'] : null;
         $this->amount   = (!empty($data['ammount'])) ? $data['ammount'] : null;
         $this->category = (!empty($data['category'])) ? $data['category'] : null;
         $this->date     = (!empty($data['date'])) ? $data['date'] : null;
    }
}