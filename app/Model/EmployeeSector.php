<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class EmployeeSector extends Model
{
    //
 
   /**
   * The database table used by the model.
   *
   * @var string 
   */
   protected $table = 'employee_sectors'; 
    
    /**
     * @var array
     */

  /**
  * The database primary key value.
  *
  * @var string
  */
  protected $guarded = ['id'];
    
   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employeeId',
        'sectorId',        
    ];

        
    public function sectors(){
        return $this->belongsTo("App\Model\Sector" ,"sectorId");
    }
}
