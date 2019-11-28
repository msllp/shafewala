<?php
/**
 * Created by PhpStorm.
 * User: ms
 * Date: 25-03-2019
 * Time: 04:35 AM
 */

namespace MS\Core\Helper;


use function GuzzleHttp\default_user_agent;

class MSForm
{



    public $returnHTML =[
        //   '<div  class="accordion">'
    ];


    public static $optionalStyleKeys=[
        'prefix'=>'prefix',
        'perfix'=>'perfix',
        'inputClass'=>'inputClass',
        'formClass'=>'formClass',
        'onlyInput'=>'inputOnly',


    ];

    public static $optionalStyleKeysWithDynamicValue=[
        'inputSize'=>'inputSize'
    ];
    public $namespace,$id,$perFix,$data,$msdb,$fields,$dbMaster,$action;
    public $newForm=true;

    public $accessAction=['add','back','edit'];

    public $attachedAction=[];

    public $formID=null;

    public $formData=[];

    /**
     * MSForm constructor.
     * @param \MS\Core\Helper\string $namespace
     * @param \MS\Core\Helper\string|null $id
     * @param \MS\Core\Helper\string|null $perFix
     * @param array $data
     */
    public function __construct(string $namespace, string $id=null, string $perFix=null, array $data=[])
    {

        $this->namespace=$namespace;
        $this->id=$id;
        $this->perFix=$perFix;
        $this->data=$data;
        $base="\\".$this->namespace."\\B";
        $this->dbMaster=$base::getModuleTables()[ $this->id];
       // $this->actionButton=[];


        foreach ($data as $v=>$k){

            switch ($v){

                case 'formID':
                    //dd($this);
                  $this->formID=$k;
                    break;

                case 'formData':
                    $this->formData=$k;
                default:


                    break;

            }

        //    dd($this);

        }




    }




    /**
     * @return array
     */
    private function getAttachedAction()
    {
        return $this->attachedAction;
    }

    /**
     * @return mixed
     */
    private function getAction()
    {
        return $this->action;
    }



    private function viewActionBtn(){
//$this->attachedAction[]='add';
        if(count($this->getAttachedAction())>0){

            $mData=[];
            $allAction=$this->getAction();
            $attachedAction=$this->getAttachedAction();
            foreach ( $attachedAction as $v){
            if(array_key_exists($v,$allAction))$mData[$v]=$allAction[$v];
            }
            //dd($mData);
            return  $this->make4VueButtonArray($mData);
        }else{
            return  $this->make4VueButtonArray($this->getAction());
        }

        return view("MS::core.layouts.Form.button.buttonGroup")->with('data',$this->action)->render();
        dd($this);
    }

    private function  make4VueButtonArray($data){
        $returnData=[];
      //  dd($data);
        foreach ($data as $type =>$btnData){
            $btn['Class']=[];
            //$btn['Class']=['btn'];
            switch ($type){
                case 'back';
                    if(is_array($btnData) && array_key_exists('btnColor',$btnData)) unset($btnData['btnColor']) ;
                    //$btn['Class'][]='btn-primary';
                    break;

                case 'add';
                    //  dd($btnData);
                    if(is_array($btnData) && array_key_exists('btnColor',$btnData)) unset($btnData['btnColor']) ;
                    //$btn['Class'][]='btn-success';
                    break;

                case 'edit';
                    if(is_array($btnData) && array_key_exists('btnColor',$btnData)) unset($btnData['btnColor']) ;
                    //$btn['Class'][]='btn-warning';
                    break;


                case 'delete';
                    if(is_array($btnData) && array_key_exists('btnColor',$btnData)) unset($btnData['btnColor']) ;
                    //$btn['Class'][]='btn-danger';
                    break;
            }

            if(is_array($btnData) && array_key_exists('btnIcon',$btnData) && $btnData['btnIcon']== "")unset($btnData['btnIcon']);
            if(is_array($btnData) && array_key_exists('route',$btnData)) $btnData['route']=route($btnData['route']);

            //if(array_key_exists('msLinkKey',$btnData)) dd($btnData);
            if(is_array($btnData))$btnData['btnClass']=implode(' ',$btn['Class']);
            //dd(in_array($type,$this->dbMaster['MSforms'][$this->formID]['actions']));
            //dd();
            if(in_array($type,$this->accessAction) && in_array($type,$this->dbMaster['MSforms'][$this->formID]['actions']))
                $returnData[$type]=$btnData;
        }


     //   dd($returnData);

        return $returnData;
        //dd($returnData);
    }

    public static function getDataFromTable($str){
        $ex=explode(':',$str);
        //dd($ex);
        $data=[];
        $rdata=[];
        if(count($ex)==3){
            $data ['namespace']=$ex[0];
            $data ['tableId']=$ex[1];
            $ex2=explode('->',$ex[2]);
            $data ['darray'][$ex2[0]]=$ex2[1];
           // dd($data);
            $m=new MSDB($data['namespace'],$data['tableId']);
           // $m->migrate();
            $d=$m->rowGet();

            $rdata['msdata']=$d;

            $rdata['value']=$ex2[0];
            $rdata['text']=$ex2[1];

            return $rdata;


        }else{
            return [];
        }


    }

    public static function makeArrayForViewFromStyle(array $array,array $data) : array{

        foreach (self::$optionalStyleKeys as $key=>$value){
            if(array_key_exists('style',$data) &&  array_key_exists($key,$data['style']))
            {
                $array[$value]=$data['style'][$key];
            }
            if(array_key_exists('validation',$data) &&  array_key_exists($key,$data['validation']))
            {

                $array[$value]=$data['validation'][$key];
            }
        }


        foreach (self::$optionalStyleKeysWithDynamicValue as $key=>$value){
            if(array_key_exists('style',$data) && array_key_exists($key,$data['style']))
            {
                switch ($key){
                    case "inputSize":
                        $strExploded=explode(".",$data['style'][$key]);

                        if(count($strExploded) > 1 ){

                            switch (count($strExploded)){

                                case 2:
                                    $array[$value]=
                                        implode(" ",
                                            [
                                                //implode("-",['col','xs',$strExploded[0]]),
                                                implode("-",['w',$strExploded[0]]),
                                                implode("-",[implode(':',['sm','w']),$strExploded[0]]),
                                                implode("-",[implode(':',['lg','w']),$strExploded[1]]),
                                     //           implode("-",['col','lg',$strExploded[3]])
                                        ]
                                        )
                                    ;
                                    break;
                                case 3:
                                    $array[$value]=
                                        implode(" ",
                                            [
                                                //implode("-",['col','xs',$strExploded[0]]),
                                                implode("-",[implode(':',['sm','w']),$strExploded[0]]),
                                                implode("-",[implode(':',['md','w']),$strExploded[1]]),
                                                implode("-",[implode(':',['lg','w']),$strExploded[2]])]
                                        )
                                    ;
                                    break;
                                case 4:
                                    $array[$value]=
                                        implode(" ",
                                        [implode("-",[implode(':',['sm','w']),$strExploded[0]]),
                                        implode("-",[implode(':',['md','w']),$strExploded[1]]),
                                        implode("-",[implode(':',['lg','w']),$strExploded[2]]),
                                        implode("-",[implode(':',['xl','w']),$strExploded[3]])]
                                        )
                                        ;
                                    break;

                            }


                        }else{
                            if(array_key_exists('style',$data) && array_key_exists($key,$data['style'])) {

                                    $array[$value] = implode('-',["w" , implode("/",[$data['style'][$key],12])]);
                            }
                        }
                        break;
                }



            }

            //var_dump($key);

            if(array_key_exists('validation',$data) && array_key_exists($key,$data['validation']))
            {
                dd(array_key_exists($key,$data['validation']));
                switch ($key){
                    case 'existIn':

                        dd($key);
                        break;

                    default:

                        dd($key);


                }


            }

        }
        //var_dump($array);
        return $array;

    }

    public function fromModel(MSDB $dbClass,$data=[]){
        $this->msdb=$dbClass;
        $this->action=$this->fields=$this->msdb->model->ms_action;;
        $this->fields=$this->msdb->model->base_Field;
        if(count($data)>0)$this->newForm=false;
        $this->makeForm();

        return $this;

    }

    private function getFieldFromFields($name){
        $fields=$this->fields;
        $field=collect($fields)->where('name',$name);

        if($field->count()> 0){
            return $field->first();
        }




    }



    /**
     *
     * This is a Function to add action button to form with action ID .
     * This action ID should described in Table File.
     * @param $actioId
     * @return void
     *
     */
    public function attachAction($actioId):void {

    $this->attachedAction[]=$actioId;

    }
    private function make4Vue($str){
            $str= strtolower(str_replace(' ','_',$str)) ;
            return $str;
    }

    private function map4Vue($array){

            return array_map(
                function ($key){
                     return  $this->make4Vue($key) ;
                },$array
            );
    }

    private function makeFieldsFromGroup($groupArray){
        $returnArray=[];

        foreach ($groupArray as $title=>$fieldsArray){
//            $returnArray[]=[
//                'vName'=>$title,
//                'input'=>'gourpHeading',
//                $title=>$fieldsArray
//
//            ];
         //   dd($fieldsArray);
            foreach ($fieldsArray as $fieldName){
                if(($fieldName == 'created_at') && ($fieldName =='updated_at')) dd($fieldName);
                if(($fieldName != 'created_at') && ($fieldName !='updated_at')){

                    //dd();
                    $aray=$this->getFieldFromFields($fieldName);
                    if($aray == null)dd($fieldName);
//                if(array_key_exists('fieldGroupMultiple',$this->dbMaster) && $this->dbMaster['fieldGroupMultiple'])
//                    $aray['groupInput']=$this->make4Vue($title);

                    //  $returnArray[$this->make4Vue($title)]['inputs'][]= $this->makeDataForVue($aray);
                    $returnArray[$this->make4Vue($title)]['gruoupHeading']=$title;
                    if(array_key_exists('fieldGroupMultiple',$this->dbMaster) && in_array($title,$this->dbMaster['fieldGroupMultiple']))
                    {
                        // if($aray == null)dd($fieldsArray);
                        //   if($aray == null)dd($aray);
                        $returnArray[$this->make4Vue($title)]['inputs'][]= $this->makeDataForVue($aray,true);
                        $returnArray[$this->make4Vue($title)]['groupDynamic']=true;

                    }else{
                        //dd($title);
                        if($aray == null)dd("ERROR 90.3 : Programing Fatal Bug in ".__CLASS__);

                        $returnArray[$this->make4Vue($title)]['inputs'][]= $this->makeDataForVue($aray);
                        $returnArray[$this->make4Vue($title)]['groupDynamic']=false;
                    }

                }



            }
//
//            $returnArray[]=[
//                'vName'=>$title,
//                'input'=>'gourpEnd',
//
//            ];

        }
       // dd($returnArray);
        return $returnArray;

    }

    private function makeForm_old(){

        if( array_key_exists('add',$this->action) ){
            $action=$this->action;
        }
        $fields=$this->fields;
        $getFields=false;
        if(array_key_exists('fieldGroup',$this->dbMaster)){
            $getFields=true;
            $fields=$this->makeFieldsFromGroup($this->dbMaster['fieldGroup'])  ;
              dd($fields);
        }

        foreach ($fields as $rowArray){



            switch ($rowArray['input']){


                case 'locked':
                    if($this->checkFromHidden($rowArray))$this->locked($rowArray);
                    break;
                case 'generated':
                    if($this->checkFromHidden($rowArray))$this->generated($rowArray);
                    break;
                case 'text':
                    if($this->checkFromHidden($rowArray))$this->addOneline($rowArray);
                    break;
                case 'number':
                    if($this->checkFromHidden($rowArray))$this->addOneline($rowArray);
                    break;
                case 'textarea':
                    if($this->checkFromHidden($rowArray))$this->textarea($rowArray);
                    break;
                case 'email':
                    if($this->checkFromHidden($rowArray))$this->addOneline($rowArray);
                    break;
                case 'password':
                    if($this->checkFromHidden($rowArray))$this->addOneline($rowArray);
                    break;
                case 'date':
                    if($this->checkFromHidden($rowArray))$this->addOneline($rowArray);
                    break;
                case 'datetime':
                    if($this->checkFromHidden($rowArray))$this->datetime($rowArray);
                    break;
                case 'checkbox':
                    if($this->checkFromHidden($rowArray))$this->checkbox($rowArray);
                    break;
                case 'radio':
                    if($this->checkFromHidden($rowArray))$this->radio($rowArray);
                    break;
                case 'option':
                    if($this->checkFromHidden($rowArray))$this->option($rowArray);
                    break;


                case 'gourpHeading':
                    $data=[
                        'title'=>$rowArray['vName'],
                        'data'=>$rowArray
                    ];
                   // $this->returnHTML[]='<div class="accordion" id="accorinf_master">';
                    $this->returnHTML[]=view("MS::core.layouts.HTML.splitButtonStart")->with('data',$data)->render();
                    break;

                case 'gourpEnd':
                    $this->returnHTML[]=view("MS::core.layouts.HTML.splitButtonEnd")->render();

                    break;

                default:

                    if($this->checkFromHidden($rowArray))$this->addOneline($rowArray);

                    break;


            }


        }


//        dd($this);
    }
    private function makeForm(){



        if( array_key_exists('add',$this->action) or  (count($this->action)>0) ){
            $action=$this->action;
        }
        $fields=$this->fields;
        $getFields=false;
        //if()

        if(array_key_exists('fieldGroup',$this->dbMaster)){
            $getFields=true;

            if($this->formID != null){
             //   dd($this->formID);
                $fData=[];
                if(array_key_exists($this->formID,$this->dbMaster['MSforms'])){
//dd($this);
                    $data=$this->dbMaster['MSforms'][$this->formID];
                  //  dd( $data);

                    foreach ($data['groups'] as $k){
                        if(array_key_exists($k,$this->dbMaster['fieldGroup']))$fData[$k]=$this->dbMaster['fieldGroup'][$k];
                    }
//dd($data);
                    $this->returnHTML['formTitle']=$data['title'];
                }
//dd($fData);


                $fields=$this->makeFieldsFromGroup($fData)  ;
             //   dd($fields);
                $this->returnHTML['formData']=$fields;
           //     if()mod_Tables


            }else{

                $fields=$this->makeFieldsFromGroup($this->dbMaster['fieldGroup'])  ;
                $this->returnHTML['formData']=$fields;

            }




//            if(array_key_exists('fieldGroupMultiple',$this->dbMaster))
//            {
//                $this->returnHTML['multipleGroup']= $this-> map4Vue( $this->dbMaster['fieldGroupMultiple']);
//            }

        }else {
            $this->returnHTML=[];
        }



        }




    private function makeDataForVue($data,$multiple=false){
      //  dd($this);

        $array=[
            'name'=>$data['name'],
            'type'=>$data['input'],
            //'vName'=>$data['vName'],
            //'prefix'=>"lock text-info",
            //'perfix'=>"asterisk text-danger",
            //'inputOnly'=>true,
            //'required'=>true,
            'validation'=>[
                // 'minSize'=>5,
                'required'=>0
            ],
            //'inputClass'=>[],
            //'formClass'=>[],

        ];


   //if($data == null ) dd($data);

        $array=\MS\Core\Helper\MSForm::makeArrayForViewFromStyle($array,$data);
//dd($array);

        if (array_key_exists('vName',$data))$array['vName']=$data['vName'];
        if (!array_key_exists('vName',$array))$array['vName']=$data['name'];
        if ((count($this->formData) > 0) && array_key_exists($data['name'],$this->formData))$array['value']=$this->formData[$data['name']];


        if(array_key_exists('style',$data)){



        }

        if(array_key_exists('validation',$data)){

            if(array_key_exists( 'required',$data['validation']) && $data['validation']['required']){
                //(array_key_exists('perfix',$array))?$array['perfix'].= " ":null;

                $array['validation']['required']=$data['validation']['required'];


            }


            if(array_key_exists('existIn',$data['validation']))$array['verifyBy']=\MS\Core\Helper\MSForm::getDataFromTable($data['validation']['existIn']);


            //$array['']=;


        }
        if(array_key_exists('inputMultiple',$data))$array['inputMultiple']=$data['inputMultiple'];
        if(array_key_exists('inputMultiple',$data))$array['inputMultiple']=$data['inputMultiple'];
        if(array_key_exists('inputInfo',$data))$array['inputInfo']=$data['inputInfo'];
        if($multiple)$array['inputMultiple']=true;
        if(array_key_exists('groupInput',$data))$array['groupInput']=$data['groupInput'];
        $f= implode("\\",[$this->namespace,"F"]) ;
        switch ($data['input']){

            case 'auto':
                goto msautogenratevalue;

                break;

            case 'password':
                if(array_key_exists('value',$array))unset($array['value']);
                break;

            case 'locked':
                msautogenratevalue:
                if(array_key_exists('callback',$data))$array['value']=call_user_func(implode("::",[$f,$data['callback']]));
                if($array['type'] != 'locked')$array['type']='locked';
                //dd($array);
                break;
        }
//dd($array);
        return $array;
        $arrayJson=collect($array)->toJson();
    }

    private function makeDataValueForView($name){
        if(array_key_exists($name,$this->formData[$name]))
        return $this->formData[$name];
        return null;
        $d=[];
        foreach ($this->fields as $input){
            if( (count($this->formData) > 0) && array_key_exists($input['name'],$this->formData)){

                switch ($input['input']){

                    case 'password' :
                        break;

                    default:
                        $d[$input['name']]=$this->formData[$input['name']];
                        break;
                }

            }
        }
        return $d;
        dd($d);

    }

    public function view(){
        //dd($this->returnHTML);

        $this->returnHTML['actionButton']=$this->viewActionBtn();
//        if(count($this->formData) > 0 ){
//            $this->returnHTML['formValue']=$this->makeDataValueForView();
//        }
        //   $this->returnHTML[]='</div>';
      //  dd($this->returnHTML);
       return view("MS::core.layouts.Form.formPlateRaw")->with("form",$this->returnHTML);
        return view("MS::core.layouts.Form.formPlate")->with("form",$this->returnHTML);
        return view("MS::core.layouts.Form.formPlate")->with("form",implode("",$this->returnHTML));
        return implode("",$this->returnHTML);
    }

    private function addOneline(array $data){
        dd($data);
        $this->returnHTML['input'][]=$data;


//        $this->returnHTML[]=view("MS::core.layouts.Form.input.oneline")->with('data',$data)->render();

    }

    private function text($data){

            $this->returnHTML[]=view("MS::core.layouts.Form.input.oneline")->with('data',$data)->render();


    }

    private function locked ($data){}
    private function generated ($data){

        $sClass=$this->namespace."\\F";

            $sMethod="::".$data['callback'];
       // dd($sClass . $sMethod);
            //  dd($input['callback']."()");
            //dd(call_user_func($sClass . $sMethod));
            $val=call_user_func($sClass . $sMethod);



        //$this->returnHTML[]=$val;



    }
    private function number ($data){}
    private function textarea ($data){}
    private function email ($data){}
    private function password ($data){}
    private function date ($data){}
    private function datetime ($data){}
    private function radio ($data){}
    private function checkbox ($data){}
    private function option ($data){}

    private function checkFromHidden($data){
        if(array_key_exists('hidden',$data) && $data['hidden'])return false;
            return true;
    }
}
