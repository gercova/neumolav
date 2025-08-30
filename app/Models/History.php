<?php

namespace App\Models;

use App\Traits\AuditLogTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class History extends Model {

    use HasFactory, SoftDeletes, AuditLogTrait;
    protected $table        = 'historias';
    protected $primaryKey   = 'id';
    protected $fillable     = ['id_td', 'dni', 'nombres', 'fecha_nacimiento', 'id_sexo', 'telefono', 'email', 'id_gs', 'ubigeo_extranjero', 'lugar_nacimiento', 'lugar_residencia', 'ubigeo_nacimiento', 'ubigeo_residencia', 'id_gi', 'ocupacion', 'id_ocupacion', 'id_estado', 'cirugias', 'transfuciones', 'traumatismos', 'hospitalizaciones', 'drogas', 'antecedentes', 'estadobasal', 'medicacion', 'animales', 'consumoagua', 'alimentacion', 'otros', 'asmabronquial', 'epoc', 'epid', 'tuberculosis', 'cancerpulmon', 'efusionpleural', 'neumonias', 'tabaquismo', 'id_ct', 'cig', 'aniosfum', 'result', 'contactotbc', 'exposicionbiomasa', 'motivoconsulta', 'sintomascardinales', 'te', 'fi', 'c', 'relatocronologico', 'estado'];
    protected $dates        = ['created_at', 'updated_at', 'deleted_at'];
    protected $casts        = [
        'id_td'             => 'integer',
        'dni'               => 'string',
        'nombres'           => 'string',
        'fecha_nacimiento'  => 'date',
        'id_sexo'           => 'string',
        'telefono'          => 'string',
        'email'             => 'string',
        'id_gs'             => 'integer',
        'ubigeo_extranjero' => 'string',
        'lugar_nacimiento'  => 'string',
        'lugar_residencia'  => 'string',
        'ubigeo_nacimiento' => 'string',
        'ubigeo_residencia' => 'string',
        'id_gi'             => 'integer',
        'ocupacion'         => 'string',
        'id_ocupacion'      => 'integer',
        'id_estado'         => 'integer',
        'cirugias'          => 'string',
        'transfuciones'     => 'string',
        'traumatismos'      => 'string',
        'hospitalizaciones' => 'string',
        'drogas'            => 'string',
        'antecedentes'      => 'string',
        'estadobasal'       => 'string',
        'medicacion'        => 'string',
        'animales'          => 'string',
        'consumoagua'       => 'string',
        'alimentacion'      => 'string',
        'otros'             => 'string',
        'asmabronquial'     => 'string',
        'epoc'              => 'string',
        'epid'              => 'string',
        'tuberculosis'      => 'string',
        'cancerpulmon'      => 'string',
        'efusionpleural'    => 'string',
        'neumonias'         => 'string',
        'tabaquismo'        => 'string',
        'id_ct'             => 'integer',
        'cig'               => 'float',
        'aniosfum'          => 'float',
        'result'            => 'float',
        'contactotbc'       => 'string',
        'exposicionbiomasa' => 'string',
        'motivoconsulta'    => 'string',
        'sintomascardinales'=> 'string',
        'te'                => 'string',
        'fi'                => 'string',
        'c'                 => 'string',
        'relatocronologico' => 'string',
        'estado'            => 'boolean',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
        'deleted_at'        => 'datetime',
    ];

    protected $tableVha, $t_udis, $t_upro, $t_udep = '';
    public function __construct() {
        parent::__construct();
        $this->tableVha = 'view_active_histories';
        $this->t_udis 	= 'ubigeo_distrito';
		$this->t_upro 	= 'ubigeo_provincia';
		$this->t_udep 	= 'ubigeo_departamento';
    }

    public static function addPatient($dni){
		return History::selectRaw('dni, UPPER(nombres) nombres, id')->where('dni', $dni)->get();
	}

    public function getAllHistories($startIndex, $pageSize, $itemSearch) {
        // Construir la consulta base
        $query = DB::table($this->tableVha)
            ->where('dni', 'LIKE', "%{$itemSearch}%")
            ->orWhere('nombres', 'LIKE', "%{$itemSearch}%");
        // Contar solo los registros que coinciden con el filtro
        $count = $query->count();
        // Obtener los resultados paginados
        $results = $query->offset($startIndex)
            ->limit($pageSize)
            ->get();
        return [$results, $count];
    }

    public function getLocation($value) {
        return DB::table($this->t_udis.' as ud')->selectRaw('ud.id as id, CONCAT(ud.id, " | ", udp.name, " | ", up.nombre_provincia, " | ", ud.nombre_distrito) as ubigeo')
            ->join($this->t_upro.' as up', 'ud.province_id', '=', 'up.id')
            ->join($this->t_udep.' as udp', 'ud.department_id', '=', 'udp.id')
            ->where('udp.name', 'LIKE', "%$value%")
            ->orWhere('ud.id', 'LIKE', "%$value%")
            ->orWhere('up.nombre_provincia', 'LIKE', "%$value%")
            ->orWhere('ud.nombre_distrito', 'LIKE', "%$value%")
            ->get()
            ->toArray();
    }

    public static function getOccupation($value) {
        return Occupation::selectRaw('CONCAT(id, " | ", descripcion) ocupacion, id')
            ->where('descripcion', 'LIKE', "%$value%")
            ->get()
            ->toArray();
    }

    public static function getOccupationByHistoryId($id){
		return History::selectRaw('(YEAR(CURRENT_DATE) - YEAR(historias.fecha_nacimiento)) - (RIGHT(CURRENT_DATE,5) < RIGHT(historias.fecha_nacimiento, 5)) AS age, CONCAT(o.id, " | ", o.descripcion) occupation')
			->join('ocupaciones as o', 'historias.id_ocupacion', '=', 'o.id')
			->where('historias.id', $id)
			->get()
			->toArray();
	}

    public static function getUBirthByHistoryId($id){
		return History::selectRaw('CONCAT(historias.ubigeo_nacimiento, " | ", udep.name, " | ", upro.nombre_provincia, " | ", udi.nombre_distrito) as nacimiento')
			->join('ubigeo_distrito as udi', 'historias.ubigeo_nacimiento', '=', 'udi.id')
			->join('ubigeo_departamento as udep', 'udi.department_id', '=', 'udep.id')
			->join('ubigeo_provincia as upro', 'udi.province_id', '=', 'upro.id')
			->where('historias.id', $id)
			->get()
			->toArray();
	}

	public static function getUResidenceByHistoryId($id){
		return History::selectRaw('CONCAT(historias.ubigeo_residencia, " | ", udep.name, " | ", upro.nombre_provincia, " | ", udi.nombre_distrito) as residencia')
			->join('ubigeo_distrito as udi', 'historias.ubigeo_residencia', '=', 'udi.id')
			->join('ubigeo_departamento as udep', 'udi.department_id', '=', 'udep.id')
			->join('ubigeo_provincia as upro', 'udi.province_id', '=', 'upro.id')
			->where('historias.id', $id)
			->get()
			->toArray();
	}

    public function documentType(){
        return $this->hasOne(DocumentType::class, 'id_td', 'id');
    }

    public function appointments() {
        return $this->hasMany(Appointment::class, 'dni', 'dni');
    }

    public function exams() {
        return $this->hasMany(Exam::class, 'dni', 'dni');
    }

    public function reports() {
        return $this->hasMany(Report::class, 'dni', 'dni');
    }

    public function risks(){
        return $this->hasMany(Risk::class, 'dni', 'dni');
    }

    public function history(){
        return $this->hasOne(History::class, 'dni', 'dni');
    }

    public function diagnosticAppointment(){
        return $this->hasMany(DiagnosticAppointment::class, 'dni', 'dni');
    }

    public function diagnosticExam(){
        return $this->hasMany(DiagnosticExam::class, 'dni', 'dni');
    }

    public function medicationExam(){
        return $this->hasMany(MedicationExam::class, 'dni', 'dni');
    }

    public function medication_appointment(){
        return $this->hasMany(MedicationAppointment::class, 'dni', 'dni');
    }

    public  function imagen(){
        return $this->hasMany(Imagen::class, 'dni', 'dni');
    }

    public function locationBirth(){
        return $this->hasOne(UbigeoDistrict::class, 'ubigeo_nacimiento', 'id');
    }

    public function locationResidence(){
        return $this->hasOne(UbigeoDistrict::class, 'ubigeo_residencia', 'id');
    }

    public function occupation(){
        return $this->hasOne(Occupation::class, 'id_occupation', 'id');
    }

    public function sex(){
        return $this->hasOne(Sex::class, 'id_sex', 'id');
    }

    public function maritalStatus(){
        return $this->hasOne(MaritalStatus::class, 'id_estado', 'id');
    }

    public function degreesInstruction(){
        return $this->hasOne(DegreesInstruction::class, 'id_gi', 'id');
    }

    public function bloodGroup(){
        return $this->hasOne(BloodGroups::class, 'id_gs', 'id');
    }

    public function smoking(){
        return $this->hasOne(Smoking::class, 'id_ct', 'id');
    }
}
