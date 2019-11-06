<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{ 
    //

    /**
   * The database table used by the model.
   *
   * @var string
   */
   protected $table = 'employees'; 
     
    /**
     * @var array
     */

   
    protected $fillable = [
        
        'mobile',
        'chamberId',
        'userId',
        'isDeleted',  
        
    ];

    public function user(){
        return $this->belongsTo("App\Model\User","userId");
    }
   
  public function chamber(){
    return $this->belongsTo("App\Model\Chamber" ,"chamberId");
    }
}
