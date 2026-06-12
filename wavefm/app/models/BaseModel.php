<?php
/**
 * BaseModel — shared database access for all models
 */
abstract class BaseModel
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}
