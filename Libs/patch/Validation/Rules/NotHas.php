<?php

namespace Validation\Rules;

use Validation\Rule;

class NotHas extends Rule
{
    use Traits\FileTrait;

    protected $message = "The :attribute is incorrect.";
    
    protected $fillable_params = ['table', 'column', 'except'];
    
    protected $db;
    
    public function __construct($db)
    {
        $this->db = $db;
    }
    
    public function check($value): bool
    {
        // make sure required parameters exists
        $this->requireParameters(['table', 'column']);
    
        // getting parameters
        $table = $this->parameter('table');
        $column = $this->parameter('column');
        $except = $this->parameter('except');

        $columns = explode('+', $column);
        foreach ($columns as $key => $val) {
            $data = explode('=', $val);
            $col = $data[0];
            $var = $data[1];
            $this->db->where($col, $var);
        }

        return (!$this->db->has($table)) ? false : true;
    }
}