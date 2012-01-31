<?php 

class cUpload {
    public $upload_id;
    public $upload_date;
    public $type; // for example "N" for "newsletters"
    public $title;
    public $filename;
    public $note;

    function __construct ($type=null, $title=null, $note=null, $filename=null) {
        global $cUser;
    
        if($type) {
            $this->type = $type;
            $this->title = $title;
            $this->note = $note;
        }
    }
    
    function SaveUpload() {
        // Copy file uploaded by UploadForm class to uploads directory and
        // save entry for it in the database
        global $cDB, $cErr;
        
        if($this->filename == null)
            $this->filename = $_FILES['userfile']['name'];
            
        $query = $cDB->Query("SELECT null from ". DATABASE_UPLOADS ." WHERE filename ='".$_FILES['userfile']['name']."';");
        
        if($row = mysql_fetch_array($query)) {
            $cErr->Error("Ya existe un documento con ese nombre en el sistema");
            return false;
        }        
            
        if(move_uploaded_file($_FILES['userfile']['tmp_name'], UPLOADS_PATH . $this->filename)) {
            $insert = $cDB->Query("INSERT INTO ". DATABASE_UPLOADS ." (type, title, filename, note) VALUES ('".$this->type ."', '". $this->title ."', '". $this->filename ."', ". $cDB->EscTxt($this->note) .");");
                        
            if(mysql_affected_rows() == 1) {
                $this->upload_id = mysql_insert_id();    
                $query = $cDB->Query("SELECT upload_date FROM ".DATABASE_UPLOADS." WHERE  upload_id=". $this->upload_id.";");
                if($row = mysql_fetch_array($query))
                    $this->upload_date = $row[0];                    
                return true;
            } else {
                $cErr->Error("No se ha podido insertar el elemento en la base de datos para el documento subido.");
                return false;
            }                
        } else {
            $cErr->Error("No se ha podido subir el elemento. Puede deberse a un problema de permisos. ¿Tiene el usuario permiso de escritura para el directorio de subidas?  Puede que el documento sea demasiado grande.  El tamaño de fichero máximo permitido es de ".MAX_FILE_UPLOAD." bytes.");
            return false;
        }
    }
    
    function LoadUpload ($upload_id) {
        global $cDB, $cErr;
                
        $query = $cDB->Query("SELECT upload_date, type, title, filename, note FROM ".DATABASE_UPLOADS." WHERE upload_id=". $upload_id.";");
        
        if($row = mysql_fetch_array($query)) {        
            $this->upload_id = $upload_id;
            $this->upload_date = new cDateTime($row[0]);
            $this->type = $row[1];        
            $this->title = $row[2];
            $this->filename = $row[3];
            $this->note = $cDB->UnEscTxt($row[4]);
            return true;
        } else {
            $cErr->Error("Ha ocurrido un error al acceder a la tabla de subidas.  Por favor, inténtelo más tarde.");
            include("redirect.php");
        }
        
    }

    function DeleteUpload () {
        global $cDB, $cErr;
        
        if(unlink(UPLOADS_PATH . $this->filename)) {
            $delete = $cDB->Query("DELETE FROM ". DATABASE_UPLOADS ." WHERE upload_id = ". $this->upload_id .";");
            if(mysql_affected_rows() == 1) {
                return true;
            } else {
                $cErr->Error("El documento se ha elminado pero no se ha podido borrar la entrada de la base de datos.  La fila deberá borrarse manualmente.  Por favor, contacta con el administrador del sistema.");
                include("redirect.php");
            }            
        } else {
            $cErr->Error("No se pudo eliminar el documento - ". $this->filename .".  Por favor, inténtelo más tarde.");
            include("redirect.php");
        }
    }

    function DisplayURL ($text=null) {
        if($text == null)
            $text = $this->title;
            
        return '<A HREF="uploads/'. $this->filename .'">'. $text .'</A>';
    }
}

class cUploadGroup {
    public $uploads; // will be object of class cUpload
    public $type;
    
    function __construct($type) {
        $this->type = $type;
    }
    
    function LoadUploadGroup () {
        global $cDB, $cErr;
    
        $query = $cDB->Query("SELECT upload_id FROM ".DATABASE_UPLOADS." WHERE type='".$this->type."' ORDER BY upload_date DESC;");
        
        $i = 0;                
        while($row = mysql_fetch_array($query)) {
            $this->uploads[$i] = new cUpload;            
            $this->uploads[$i]->LoadUpload($row[0]);
            $i += 1;
        }

        if($i == 0)
            return false;
        else
            return true;
    }
    

}

class cUploadForm {

    function DisplayUploadForm($action, $text_fields=null) {
    
    $output = '<form enctype="multipart/form-data" action="'. $action.'" method="POST">';
    foreach($text_fields as $field)
        $output .= $field .' <input type="text" name="'. $field .'"><BR>';
        
    $output .= '<input type="hidden" name="MAX_FILE_SIZE" value="'.MAX_FILE_UPLOAD.'">Elige el archivo a subir <input name="userfile" type="file"><input type="submit" value="Subir"></form>';
    return $output;
    }

}

?>
