<?php
// app/Models/UserModel.php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'email', 'password'];
    protected $useTimestamps = true;

    // Method untuk mencari user berdasarkan email
    public function findByEmail($email)
    {
        return $this->where('email', $email)->first();
    }
}