<?php

/**
 * Nette Framework
 *
 * Copyright (c) 2004, 2008 David Grudl (http://davidgrudl.com)
 *
 * This source file is subject to the "Nette license" that is bundled
 * with this package in the file license.txt.
 *
 * For more information please see http://nettephp.com/
 *
 * @copyright  Copyright (c) 2004, 2008 David Grudl
 * @license    http://nettephp.com/license  Nette license
 * @link       http://nettephp.com/
 * @category   Nette
 * @package    Nette::Forms
 */

interface
IFormControl{function
setValue($value);function
getValue();function
processHttpData($data);}interface
IComponent{function
getId();function
getUniqueId();function
getParent();function
setParent(IContainer$parent=NULL,$id=NULL);function
setServiceLocator(IServiceLocator$locator);function
getServiceLocator();}interface
IContainer{function
addComponent(IComponent$component,$id);function
removeComponent(IComponent$component);function
getComponent($id);function
getComponents();}define('event',"");interface
IServiceLocator{function
addService($service,$promote=FALSE);function
removeService($type,$promote=FALSE);function
getService($type);function
getParent();}class
NServiceLocator
extends
Object
implements
IServiceLocator{private
static$instance;private$parent;private$registry=array();public
function
__construct(IServiceLocator$parent=NULL){$this->parent=$parent;}final
public
static
function
getInstance(){if(self::$instance===NULL){self::$instance=new
self;}return
self::$instance;}public
function
addService($service,$promote=FALSE){if(is_object($service)){$item=get_class($service);}elseif(is_string($service)){$item=$service;}elseif(is_callable($service,TRUE)){list(,$item)=$service;}else{throw
new
Exception('Service must be class/interface name of object');}foreach(class_implements($item)as$class){$this->registry[strtolower($class)][]=$service;}foreach(class_parents($item)as$class){$this->registry[strtolower($class)][]=$service;}$this->registry[strtolower($item)][]=$service;if($promote&&$this->parent!==NULL){$this->parent->addService($service,TRUE);}}public
function
removeService($type,$promote=FALSE){if(!is_string($type)){throw
new
Exception('Service must be class/interface name');}if($promote&&$this->parent!==NULL){$this->parent->removeService($type,TRUE);}}public
function
getService($type,$default=NULL){if(!is_string($type)||(!class_exists($type,FALSE)&&!interface_exists($type,FALSE))){throw
new
Exception('Service must be class/interface name');}$type=strtolower($type);if(isset($this->registry[$type])){if(count($this->registry[$type])>1){throw
new
Exception('Ambiguous service resolution');}$obj=$this->registry[$type][0];if(is_object($obj)){return$obj;}elseif(is_string($obj)){return$this->registry[$type][0]=new$obj;}else{return$this->registry[$type][0]=call_user_func($obj);}}else{$obj=$this->parent===NULL?NULL:$this->parent->getService($type);if($obj===NULL&&$default!==NULL){if(is_object($default)){return$default;}if(is_string($default)){return
new$default;}else{throw
new
Exception('Default service must be class/interface name of object');}}return$obj;}}public
function
getParent(){return$this->parent;}}abstract
class
NComponent
extends
Object
implements
IComponent{const
ID_SEPARATOR='-';private$serviceLocator;private$parent;private$id;private$uniqueId;private$lookupCache=array();public
function
__construct(IContainer$parent=NULL,$id=NULL){if($parent!==NULL){$parent->addComponent($this,$id);}elseif(is_string($id)){$this->uniqueId=$this->id=$id;}if($this->serviceLocator===NULL){$this->serviceLocator=NServiceLocator::getInstance();}$this->constructed();}protected
function
constructed(){}public
function
lookup($type){if(array_key_exists($type,$this->lookupCache)){return$this->lookupCache[$type];}$obj=$this;do{if($obj
instanceof$type)break;$obj=$obj->getParent();}while($obj!==NULL);return$this->lookupCache[$type]=$obj;}final
public
function
getId(){return$this->id;}final
public
function
getUniqueId(){return$this->uniqueId;}final
public
function
getParent(){return$this->parent;}public
function
setParent(IContainer$parent=NULL,$id=NULL){if($this->parent===$parent)return;if($this->parent!==NULL&&$parent!==NULL){throw
new
Exception('Component already has a parent');}if($parent===NULL){if($this->parent->getComponent($this->id)===$this){throw
new
Exception('The current parent still recognizes this component as its child');}}else{if($parent->getComponent($id)!==$this){throw
new
Exception('The given parent does not recognize this component as its child');}$this->validateParent($parent);}$this->beforeHierarchyChange($this);$this->parent=$parent;if($id!==NULL)$this->id=$id;$this->refreshCache();$this->afterHierarchyChange($this);}protected
function
validateParent(IContainer$parent){}protected
function
beforeHierarchyChange(IComponent$sender){}protected
function
afterHierarchyChange(IComponent$sender){}protected
function
refreshCache(){$this->lookupCache=array();$parentId=$this->parent===NULL?'':$this->parent->getUniqueId();$this->uniqueId=$parentId==NULL?$this->id:$parentId.self::ID_SEPARATOR.$this->id;}public
function
setServiceLocator(IServiceLocator$locator){$this->serviceLocator=$locator;}final
public
function
getServiceLocator(){return$this->serviceLocator;}final
public
function
getService($type){return$this->serviceLocator->getService($type);}public
function
__clone(){if($this->parent!==NULL&&!($this->parent
instanceof
NContainer&&$this->parent->isCloning())){$this->setParent(NULL);}}final
public
function
__sleep(){throw
new
Exception('Serialization is not implemented yet');}}abstract
class
NContainer
extends
NComponent
implements
IContainer{private$components=array();private$cloning=FALSE;public
function
addComponent(IComponent$component,$id){if($id===NULL){$id=$component->getId();}if($id==NULL){throw
new
Exception('Component ID is required');}if(!is_string($id)||strpos($id,self::ID_SEPARATOR)!==FALSE){throw
new
Exception("Component ID must be non-empty alphanumeric string, '$id' is invalid");}if(isset($this->components[$id])){throw
new
Exception("Component with ID '$id' already exists");}$obj=$this;do{if($obj===$component){throw
new
Exception("Recursion is forbidden");}$obj=$obj->getParent();}while($obj!==NULL);$this->validateChildComponent($component);try{$this->components[$id]=$component;$component->setParent($this,$id);$component->setServiceLocator(new
NServiceLocator($this->serviceLocator));}catch(Exception$e){unset($this->components[$id]);throw$e;}}public
function
removeComponent(IComponent$component){$id=$component->getId();if(!isset($this->components[$id])||$this->components[$id]!==$component){throw
new
Exception("Component is not located in this container");}unset($this->components[$id]);$component->setParent(NULL);}final
public
function
getComponent($id,$need=FALSE){if(isset($this->components[$id])){return$this->components[$id];}elseif($need){throw
new
Exception("Component with id '$id' didn't exist");}else{return
NULL;}}final
public
function
getComponents(){return$this->components;}protected
function
validateChildComponent(IComponent$child){}protected
function
beforeHierarchyChange(IComponent$sender){parent::beforeHierarchyChange($sender);$this->notifyComponents('beforeHierarchyChange',array($sender),'NComponent',FALSE);}protected
function
afterHierarchyChange(IComponent$sender){parent::afterHierarchyChange($sender);$this->notifyComponents('afterHierarchyChange',array($sender),'NComponent',TRUE);}protected
function
refreshCache(){parent::refreshCache();$this->notifyComponents('refreshCache',array(),'NComponent');}protected
function
notifyComponents($method,array$args=array(),$type='NComponent',$depthFirst=TRUE){foreach($this->components
as$component){if(!$depthFirst&&$component
instanceof
self){$component->notifyComponents($method,$args,$type,$depthFirst);}if($component
instanceof$type){call_user_func_array(array($component,$method),$args);}if($depthFirst&&$component
instanceof
self){$component->notifyComponents($method,$args,$type,$depthFirst);}}}public
function
__clone(){parent::__clone();if($this->components){$oldMyself=reset($this->components)->getParent();$oldMyself->cloning=TRUE;foreach($this->components
as$id=>$component){$this->components[$id]=clone$component;}$oldMyself->cloning=FALSE;}}public
function
isCloning(){return$this->cloning;}}class
FormRules
extends
Object{protected$rules=array();protected$form;public
static$requiredClass='required';public
function
__construct(Form$form){$this->form=$form;}public
function
addRule($name,$operation,$message,$arg=NULL){$item=$this->form[$name];$item->notifyRule(TRUE,$operation,$arg);$this->form->validateMode|=Form::VM_CENTRAL;$this->rules[]=array('item'=>$item,'operation'=>$operation<0?~$operation:$operation,'arg'=>$arg,'message'=>vsprintf($message,(array)$arg),'toggle'=>NULL,'subrules'=>NULL,'neg'=>$operation<0,'script'=>NULL,);}public
function
addCondition($name,$operation,$arg=NULL,$toggle=NULL){$item=$this->form[$name];$item->notifyRule(FALSE,$operation,$arg);if($toggle)$item->setEvent($this->form->toggleFunction."(this);");$subrules=new
self($this->form);$this->rules[]=array('item'=>$item,'operation'=>$operation<0?~$operation:$operation,'arg'=>$arg,'message'=>NULL,'toggle'=>$toggle,'subrules'=>$subrules,'neg'=>$operation<0,'script'=>NULL,);return$subrules;}public
function
addScript($script){$this->rules[]=array('script'=>$script,);}public
function
validate(){$valid=TRUE;foreach($this->rules
as$rule){extract($rule);if($script)continue;$ok=($neg
xor$item->validate($operation,$arg));if(!$ok&&$message){$this->form->addError($message,$item->getUniqueId());$valid=FALSE;}if($ok&&$subrules){$ok=$subrules->validate();$valid=$valid&&$ok;}}return$valid;}public
function
getValidateScript(){$res='';foreach($this->rules
as$rule){extract($rule);if($script){$res.=$script."\n\t";continue;}$script=$item->getClientScript($operation,$arg);if(!$script)continue;$res.="$script\n\t";if($message){$res.="if (".($neg?'':'!')."res) { "."if (el) el.focus(); alert(".json_encode((string)$message)."); return false; }\n\t";}if($subrules){$script=$subrules->getValidateScript();if($script)$res.="if (".($neg?'!':'')."res) {\n\t".$script."}\n\t";}}return$res;}public
function
getToggleScript(){$res='';foreach($this->rules
as$rule){extract($rule);if($script)continue;if($toggle){$script=$item->getClientScript($operation,$arg);if($script){$res.=$script."\n\t"."el = document.getElementById('".$toggle."');\n\t"."if (el) el.style.display = ".($neg?'!':'')."res ? 'block' : 'none';\n\t";}}if($subrules)$res.=$subrules->getToggleScript();}return$res;}}abstract
class
FormItem
extends
NComponent
implements
IFormControl{public
static$idCounter=1;public
static$idMask='frm-%';protected$value;protected$label;protected$event;public$classes;protected$hint;protected$required;protected$htmlId;public$validators=array();public
function
__construct(IContainer$parent=NULL,$name=NULL,$label=NULL){parent::__construct($parent,$name);$this->htmlId=str_replace('%',self::$idCounter++,self::$idMask);$this->label=$label;}protected
function
validateParent(IContainer$parent){if(!$parent->lookup('Form')){throw
new
FormItemException('Form not found');}}public
function
setValue($value){$this->value=$value;}public
function
getValue(){return$this->value;}public
function
processHttpData($data){$name=$this->getUniqueId();$this->setValue(isset($data[$name])?$data[$name]:NULL);}abstract
public
function
getControl();public
function
getLabel(){return
Html::el('label',array('for'=>$this->htmlId,'class'=>$this->required?'required':NULL,))->setText($this->label);}public
function
setEvent($event){$this->event=$event;}public
function
getEvent(){return$this->event;}public
function
isSubmitted(){return
FALSE;}public
function
validate($operation,$arg){switch($operation){case
Form::EQUAL:if(is_object($arg))return
get_class($arg)===$this->getClass()?($this->value==$arg->value):FALSE;return$this->value==$arg;case
Form::FILLED:return(string)$this->value!=='';case
Form::SUBMITTED:return$this->isSubmitted();}return
FALSE;}public
function
getClientScript($operation,$arg){}public
function
notifyRule($isRule,$operation,$arg){if($isRule&&$operation===Form::FILLED){$this->required=TRUE;}}}class
FormTextItem
extends
FormItem{public$emptyValue='';private$size;protected$type='text';private$maxlength;protected$value='';protected$rawValue;public$filters=array();public
function
__construct(IContainer$parent=NULL,$name,$label,$size=NULL){$this->size=$size;$this->filters[]='trim';parent::__construct($parent,$name,$label);}public
function
getControl(){return
Html::el('input',array('type'=>$this->type,'name'=>$this->getUniqueId(),'value'=>$this->value===''?$this->emptyValue:$this->rawValue,'size'=>$this->size,'maxlength'=>$this->maxlength,'id'=>$this->htmlId,'class'=>$this->classes,'onclick'=>$this->event,));}public
function
setValue($value){$value=(string)$value;foreach($this->filters
as$filter){$value=(string)call_user_func($filter,$value);}$this->rawValue=$this->value=$value===$this->emptyValue?'':$value;}public
function
processHttpData($data){$name=$this->getUniqueId();$rawValue=isset($data[$name])?$data[$name]:NULL;$this->setValue($rawValue);$this->rawValue=$rawValue;}public
function
notifyRule($isRule,$operation,$arg){if($isRule&&$operation===Form::LENGTH)$this->maxlength=$arg[1];elseif($isRule&&$operation===Form::MAX_LENGTH)$this->maxlength=$arg;elseif($operation===Form::REGEXP&&$arg[0]!=='/')throw
new
Exception('Invalid regexp');parent::notifyRule($isRule,$operation,$arg);}public
function
validate($operation,$arg){switch($operation){case
Form::MIN_LENGTH:return
iconv_strlen($this->value)>=$arg;case
Form::MAX_LENGTH:return
iconv_strlen($this->value)<=$arg;case
Form::LENGTH:return
iconv_strlen($this->value)>=$arg[0]&&iconv_strlen($this->value)<=$arg[1];case
Form::EMAIL:return
preg_match('/^[^@]+@[^@]+\.[a-z]{2,6}$/i',$this->value);case
Form::URL:return
preg_match('/^.+\.[a-z]{2,6}(\\/.*)?$/i',$this->value);case
Form::REGEXP:return
preg_match($arg,$this->value);case
Form::NUMERIC:return
preg_match('/^-?[0-9]+$/',$this->value);case
Form::FLOAT:return
preg_match('/^-?[0-9]*[.,]?[0-9]+$/',$this->value);case
Form::RANGE:return$this->value>=$arg[0]&&$this->value<=$arg[1];}return
parent::validate($operation,$arg);}public
function
getClientScript($operation,$arg){$tmp="el = document.getElementById('".$this->htmlId."');\n\t"."var val = el.value.replace(/^\\s+/, '').replace(/\\s+\$/, '');\n\t";switch($operation){case
Form::EQUAL:if(is_object($arg))return
get_class($arg)===$this->getClass()?$tmp."res = val==document.getElementById('".$arg->htmlId."').value;":'res = false;';return$tmp."res = val==".json_encode((string)$arg).";";case
Form::FILLED:return$tmp."res = val!='' && val!=".json_encode((string)$this->emptyValue).";";case
Form::MIN_LENGTH:return$tmp."res = val.length>=".(int)$arg.";";case
Form::MAX_LENGTH:return$tmp."res = val.length<=".(int)$arg.";";case
Form::LENGTH:return$tmp."res = val.length>=".(int)$arg[0]." && val.length<=".(int)$arg[1].";";case
Form::EMAIL:return$tmp.'res = /^[^@]+@[^@]+\.[a-z]{2,6}$/i.test(val);';case
Form::URL:return$tmp.'res = /^.+\.[a-z]{2,6}(\\/.*)?$/i.test(val);';case
Form::REGEXP:return$tmp."res = $arg.test(val);";case
Form::NUMERIC:return$tmp."res = /^-?[0-9]+$/.test(val);";case
Form::FLOAT:return$tmp."res = /^-?[0-9]*[.,]?[0-9]+$/.test(val);";case
Form::RANGE:return$tmp."res = parseFloat(val)>=".(int)$arg[0]." && parseFloat(val)<=".(int)$arg[1].";";}return
FALSE;}}class
FormPasswordItem
extends
FormTextItem{protected$type='password';}class
FormTextAreaItem
extends
FormTextItem{private$cols;private$rows;public
function
__construct(IContainer$parent=NULL,$name,$label,$cols,$rows){$this->cols=(int)$cols;$this->rows=(int)$rows;parent::__construct($parent,$name,$label);$this->filters=array();}public
function
getControl(){return
Html::el('textarea',array('name'=>$this->getUniqueId(),'cols'=>$this->cols,'rows'=>$this->rows,'id'=>$this->htmlId,'class'=>$this->classes,'onclick'=>$this->event,))->setText($this->value===''?$this->emptyValue:$this->value);}}class
FormFileItem
extends
FormItem{public
function
getControl(){return
Html::el('input',array('type'=>'file','name'=>$this->getUniqueId(),'id'=>$this->htmlId,'class'=>$this->classes,'onclick'=>$this->event,));}public
function
setValue($value){$this->value=is_array($value)&&is_uploaded_file($value['tmp_name'])?$value:NULL;}public
function
validate($operation,$arg){switch($operation){case
Form::FILLED:return$this->isOK();case
Form::MAX_FILE_SIZE:return
FALSE;case
Form::MIME_TYPE:return
FALSE;}return
parent::validate($operation,$arg);}public
function
isOK(){return$this->value&&$this->value['error']===UPLOAD_ERR_OK;}public
function
move($dest){return
move_uploaded_file($this->value['tmp_name'],$dest);}public
function
getImageSize(){return
getimagesize($this->value['tmp_name']);}}class
FormHiddenItem
extends
FormItem{protected$value='';public
function
getControl(){return
Html::el('input',array('type'=>'hidden','name'=>$this->getUniqueId(),'value'=>$this->value,));}public
function
getLabel(){return
NULL;}public
function
setValue($value){$this->value=(string)$value;}}class
FormButtonItem
extends
FormItem{protected$value=FALSE;public
function
setValue($value){}public
function
getControl(){return
Html::el('input',array('type'=>'button','name'=>$this->getUniqueId(),'value'=>$this->label,'class'=>$this->classes,'onclick'=>$this->event,));}public
function
getLabel(){return
NULL;}}class
FormSubmitButtonItem
extends
FormButtonItem{public$onClick=event;public
function
getControl(){$el=parent::getControl();$el->type='submit';return$el;}public
function
processHttpData($data){$this->value=isset($data[$this->getUniqueId()]);if($this->value){$this->lookup('Form')->setSubmittedBy($this);}}public
function
isSubmitted(){return$this->value;}public
function
notifyRule($isRule,$operation,$arg){if($operation===Form::SUBMITTED||$operation===~Form::SUBMITTED){$form=$this->lookup('Form');$form->validateMode=Form::VM_BUTTON;$this->event.='return '.$form->validateFunction."(this);";}parent::notifyRule($isRule,$operation,$arg);}public
function
getClientScript($operation,$arg){if($operation===Form::SUBMITTED){return"el=null; res=sender && sender.name==".json_encode($this->getUniqueId()).";";}return
FALSE;}}class
FormImageItem
extends
FormSubmitButtonItem{private$src;private$alt;public
function
__construct(IContainer$parent=NULL,$name,$src,$alt){$this->src=$src;$this->alt=$alt;parent::__construct($parent,$name,NULL);}public
function
getControl(){return
Html::el('input',array('type'=>'image','name'=>$this->getUniqueId(),'src'=>$this->src,'alt'=>$this->alt,'class'=>$this->classes,'onclick'=>$this->event,));}public
function
processHttpData($data){$this->value=isset($data[$this->getUniqueId().'_x']);if($this->value){$this->lookup('Form')->setSubmittedBy($this);}}}class
FormCheckboxItem
extends
FormItem{protected$value=FALSE;public
function
getControl(){return
Html::el('input',array('type'=>'checkbox','name'=>$this->getUniqueId(),'checked'=>$this->value,'id'=>$this->htmlId,'class'=>$this->classes,'onclick'=>$this->event,));}public
function
setValue($value){$this->value=(bool)$value;}public
function
getClientScript($operation,$arg){if($operation===Form::EQUAL){return"el = document.getElementById('".$this->htmlId."');\n\tres = ".($arg?'':'!')."el.checked;";}return
FALSE;}}class
FormRadioItem
extends
FormItem{protected$items;public
function
__construct(IContainer$parent=NULL,$name,$label,$items){if(!is_array($items)){throw
new
Exception('Items must be array.');}$this->items=$items;parent::__construct($parent,$name,$label);}public
function
getControl(){$el=Html::el();$name=$this->getUniqueId();$counter=0;foreach($this->items
as$key=>$val){$id=$this->htmlId.'-'.$counter;$counter++;$el->create('input',array('type'=>'radio','name'=>$name,'checked'=>$key==$this->value,'value'=>$key,'id'=>$id,'class'=>$this->classes,'onclick'=>$this->event,));$el->create('label',$val)->for($id);$el->create('br');}return$el;}public
function
getLabel(){$label=parent::getLabel();$label->for=NULL;return$label;}public
function
setValue($value){$this->value=isset($this->items[$value])?$value:NULL;}public
function
validate($operation,$arg){if($operation===Form::FILLED){return$this->value!==NULL;}return
parent::validate($operation,$arg);}public
function
getClientScript($operation,$arg){switch($operation){case
Form::EQUAL:return"res = false;\n\t"."for (var i=0;i<".count($this->items).";i++) {\n\t\t"."el = document.getElementById('".$this->htmlId."-'+i);\n\t\t"."if (el.checked && el.value==".json_encode((string)$arg).") { res = true; break; }\n\t"."}\n\tel = null;";case
Form::FILLED:return"res = false; el=null;\n\t"."for (var i=0;i<".count($this->items).";i++) "."if (document.getElementById('".$this->htmlId."-'+i).checked) { res = true; break; }";}return
FALSE;}}class
FormSelectItem
extends
FormItem{private$items;protected$allowed;public$skipFirst=FALSE;private$size;protected$value=NULL;public
function
__construct(IContainer$parent=NULL,$name,$label,$items,$size){if(!is_array($items)){throw
new
Exception('Items must be array.');}$this->items=$items;$this->size=$size;$this->allowed=array();foreach($items
as$key=>$value){if(is_array($value)){foreach($value
as$key2=>$value2){$this->allowed[$key2]=TRUE;}}else{$this->allowed[$key]=TRUE;}}parent::__construct($parent,$name,$label);}public
function
getControl(){$el=Html::el('select',array('name'=>$this->getUniqueId(),'size'=>$this->size>1?$this->size:NULL,'id'=>$this->htmlId,'class'=>$this->classes,'onmousewheel'=>'return false','onchange'=>$this->event,));$selected=array_flip((array)$this->value);foreach($this->items
as$key=>$value){if(is_array($value)){$group=$el->create('optgroup')->label($key);foreach($value
as$key2=>$value2){$group->create('option',$value2)->value($key2)->selected(isset($selected[$key2]));}}else{$el->create('option',$value)->value($key)->selected(isset($selected[$key]));}}return$el;}public
function
getLabel(){$label=parent::getLabel();$label->onclick='return false';return$label;}public
function
setValue($value){$allowed=$this->allowed;if($this->skipFirst)array_shift($allowed);$this->value=isset($allowed[$value])?$value:NULL;}public
function
validate($operation,$arg){if($operation===Form::FILLED){return$this->value!==NULL;}return
parent::validate($operation,$arg);}public
function
getClientScript($operation,$arg){$tmp="el = document.getElementById('".$this->htmlId."');\n\t";$first=$this->skipFirst?1:0;switch($operation){case
Form::EQUAL:return$tmp."res = false;\n\t"."for (var i=$first;i<el.options.length;i++)\n\t\t"."if (el.options[i].selected && el.options[i].value==".json_encode((string)$arg).") { res = true; break; }";case
Form::FILLED:return$tmp."res = el.selectedIndex >= $first;";}return
FALSE;}}class
FormMultiSelectItem
extends
FormSelectItem{protected$value=array();public
function
getControl(){$el=parent::getControl();$el->name.='[]';$el->multiple=TRUE;return$el;}public
function
setValue($value){$allowed=$this->allowed;if($this->skipFirst)array_shift($allowed);$this->value=array();foreach((array)$value
as$val){if(isset($allowed[$val])){$this->value[]=$val;}}}public
function
validate($operation,$arg){if($operation===Form::FILLED){return
count($this->value)>0;}return
parent::validate($operation,$arg);}}class
Form
extends
NContainer
implements
ArrayAccess{const
EQUAL=10,FILLED=11,SUBMITTED=12,MIN_LENGTH=13,MAX_LENGTH=14,LENGTH=15,EMAIL=16,URL=17,REGEXP=18,NUMERIC=19,FLOAT=20,RANGE=21,MAX_FILE_SIZE=22,MIME_TYPE=23;const
TRACKER_ID='_form_';const
VM_CENTRAL=1;const
VM_BUTTON=3;private$el;private$isPost;private$populated=FALSE;private$submittedBy;private$valid;private$rules;private$errors;public$validateFunction,$toggleFunction;public$validateMode=0;public$onSubmit=event;public
function
__construct(IContainer$parent=NULL,$id=NULL,$isPost=TRUE){$this->isPost=$isPost;$this->rules=new
FormRules($this);$this->el=Html::el('form');$this->el->action='';$this->el->method=$isPost?'post':'get';parent::__construct($parent,$id);$name=ucfirst(strtr($this->getUniqueId(),self::ID_SEPARATOR,'_'));$this->validateFunction='validate'.$name;$this->toggleFunction='toggle'.$name;}public
function
isSubmitted($by=NULL){if($this->_isSubmitted()&&!$this->populated){$this->populate();}if($by===NULL){return$this->submittedBy;}if(is_string($by)){$by=$this->getComponent($by,TRUE);}return$this->submittedBy===$by;}private
function
_isSubmitted(){if($this->submittedBy===NULL){$this->detectSubmission();}return$this->submittedBy;}public
function
setSubmittedBy(IFormControl$by=NULL){$this->submittedBy=$by===NULL?FALSE:$by;}public
function
executeSubmit(){if($this->isSubmitted()){if($this->submittedBy
instanceof
FormItem){$this->submittedBy->onClick($this->submittedBy);}$this->onSubmit($this,$this->submittedBy);}}private
function
detectSubmission(){$this->submittedBy=FALSE;if($this->isPost
xor@$_SERVER['REQUEST_METHOD']==='POST')return;$tracker=$this->getComponent(self::TRACKER_ID);if($tracker){if($this->isPost){if(!isset($_POST[self::TRACKER_ID]))return;if($_POST[self::TRACKER_ID]!==$tracker->getValue())return;}else{if(!isset($_GET[self::TRACKER_ID]))return;if($_GET[self::TRACKER_ID]!==$tracker->getValue())return;}}$this->submittedBy=TRUE;}public
function
setDefaults(array$values){$tracker=$this->getComponent(self::TRACKER_ID);if($tracker){$values[self::TRACKER_ID]=$tracker->getValue();}foreach($this->getComponents()as$key=>$item){if($item
instanceof
IFormControl){$item->setValue(isset($values[$key])?$values[$key]:NULL);}}$this->populated=TRUE;}public
function
populate(){if(!$this->_isSubmitted()){throw
new
Exception('Form is not submitted. Use method setDefaults() to populate data');}if(@$_SERVER['REQUEST_METHOD']==='POST'){$data=$_POST+$_FILES;}else{$data=$_GET;}$this->notifyComponents('processHttpData',array($data),'IFormControl',FALSE);$this->populated=TRUE;if(!is_object($this->submittedBy)){foreach($this->getComponents()as$item){}$this->submittedBy=$this;}}public
function
isPopulated(){return$this->populated;}public
function
getValues(){if(!$this->populated){$this->populate();}$values=array();foreach($this->getComponents()as$key=>$item){if($item
instanceof
IFormControl){$values[$key]=$item->getValue();}}unset($values[self::TRACKER_ID]);return$values;}public
function
setAction($URL){return$this->el->action=$URL;}public
function
getFormElement(){if($this->validateMode===self::VM_CENTRAL){$this->el->onsubmit="return ".$this->validateFunction."(this)";}return$this->el;}public
function
isValid(){if($this->valid===NULL){$this->validate();}return$this->valid;}public
function
validate(){if(!$this->populated){$this->populate();}$this->errors=array();$this->valid=$this->rules->validate();}public
function
addError($message,$name=NULL){if($name!==NULL)$this->errors[$name][]=$message;$this->errors[NULL][]=$message;}public
function
getErrors($name=NULL){return
isset($this->errors[$name])?$this->errors[$name]:NULL;}public
function
hasErrors($name=NULL){return
isset($this->errors[$name])?(bool)$this->errors[$name]:FALSE;}public
function
addText($name,$label,$size=NULL){$item=new
FormTextItem($this,$name,$label,$size);$item->classes[]='text';return$item;}public
function
addPassword($name,$label,$size=NULL){$item=new
FormPasswordItem($this,$name,$label,$size);$item->classes[]='text';return$item;}public
function
addTextArea($name,$label,$cols,$rows){return
new
FormTextAreaItem($this,$name,$label,$cols,$rows);}public
function
addFile($name,$label){$this->el->enctype='multipart/form-data';$item=new
FormFileItem($this,$name,$label);$item->classes[]='text';return$item;}public
function
addHidden($name){return
new
FormHiddenItem($this,$name);}public
function
addCheckbox($name,$label){return
new
FormCheckboxItem($this,$name,$label);}public
function
addRadio($name,$label,$items){return
new
FormRadioItem($this,$name,$label,$items);}public
function
addSelect($name,$label,$items,$size=1){return
new
FormSelectItem($this,$name,$label,$items,$size);}public
function
addMultiSelect($name,$label,$items,$size=1){return
new
FormMultiSelectItem($this,$name,$label,$items,$size);}public
function
addSubmit($name,$label){$item=new
FormSubmitButtonItem($this,$name,$label,TRUE);$item->classes[]='button';return$item;}public
function
addButton($name,$label){$item=new
FormButtonItem($this,$name,$label,FALSE);$item->classes[]='button';return$item;}public
function
addImage($name,$src,$alt){return
new
FormImageItem($this,$name,$src,$alt);}public
function
addTracker($name){$tracker=new
FormHiddenItem($this,self::TRACKER_ID);$tracker->setValue($name);}public
function
addRule($name,$operation,$message,$arg=NULL){$this->rules->addRule($name,$operation,$message,$arg);}public
function
addCondition($name,$operation,$value=NULL,$toggle=NULL){return$this->rules->addCondition($name,$operation,$value,$toggle);}public
function
renderErrors($name=NULL){if(empty($this->errors[$name]))return
NULL;$ul=Html::el('ul')->class('error');foreach($this->errors[$name]as$error)$ul->create('li',$error);echo$ul;}public
function
renderBegin(){echo$this->getFormElement()->startTag();}public
function
renderEnd(){echo$this->getFormElement()->endTag();echo$this->renderClientScript();}public
function
renderClientScript(){$validateScript=$this->rules->getValidateScript();$toggleScript=$this->rules->getToggleScript();if(!$validateScript&&!$toggleScript)return;echo"<script type=\"text/javascript\">\n","/* <![CDATA[ */\n";if($validateScript)echo"function $this->validateFunction(sender) {\n\t","var el, res;\n\t",$validateScript,"return true;\n","}\n\n";if($toggleScript)echo"function $this->toggleFunction(sender) {\n\t","var el, res;\n\t",$toggleScript,"\n}\n\n","$this->toggleFunction(null);\n";echo"/* ]]> */\n","</script>\n";}public
function
renderForm(){$this->renderBegin();if($this->isSubmitted())echo"\n",$this->renderErrors();$hidden=Html::el('div');echo"\n<table>\n";foreach($this->getComponents()as$item){if($item
instanceof
FormHiddenItem){$hidden->add($item->getControl());}elseif($item
instanceof
FormCheckboxItem){echo"<tr>\n\t<th>&nbsp;</th>\n\t<td>",$item->control,$item->label,"</td>\n</tr>\n\n";}elseif($item
instanceof
FormItem){echo"<tr>\n\t<th>",($item->label?$item->label:'&nbsp;'),"</th>\n\t<td>",$item->control,"</td>\n</tr>\n\n";}}echo"</table>\n";if($hidden->count())echo$hidden;$this->renderEnd();}final
public
function
offsetSet($id,$component){$this->addComponent($component,$id);}final
public
function
offsetGet($id){return$this->getComponent($id,TRUE);}final
public
function
offsetExists($id){return$this->getComponent($id)!==NULL;}final
public
function
offsetUnset($id){$this->removeComponent($this->getComponent($id,TRUE));}}
