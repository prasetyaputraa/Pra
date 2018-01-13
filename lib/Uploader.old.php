<?php

class Uploader
{
  public $replacedErrMsg = array();
  
  protected $destination;

  protected $default_permissions = 0750;
  protected $maxSize             = 1000000;
  protected $nameLimit           = 100;

  protected $fileName;
  protected $fileSize;
  protected $filePath;

  protected $prefixIdentifier = '';

  protected $errors      = array();
  protected $mimesBinary = array(
    //image type
    'image/jpeg' => "\xFF\xd8\xFF",
    'image/png'  => "\x89\x50\x4e\x47\x0d\x0a\x1a\x0a",
    'image/gif'  => "\x47\x49\x46\x38\x39\x61",

    //application type
    'application/pdf' => "\x25\x50\x44\x46" ,

    //audio type
    'audio/aac' => "\xFF\xF1",

    //video type
    'video/mpeg' => "\x00\x00\x01\xba",

    //all type
    'all' => ""
  );

  protected $allowedMimesBinary = array();

  private $renameIfTooLong = false;

  public function __construct($destination)
  {
    $this->setDestination($destination);
  }

  public function upload($file)
 {
    if ($file, $this->validate(array('rename' => $this->renameIfTooLong))) {
      $this->fileName = $this->setNewName();

      if (move_uploaded_file($this->filePath, $this->destination . $this->fileName)) {
        chmod($this->destination . $this->fileName, $this->default_permissions);
        return $this->fileName;
      }
    }
    
    return false;
  }

  public function deleteFile($fileName)
  {
    if (file_exists($this->destination . $fileName)) {
      return unlink($this->destination . $fileName);
    }

    return false;
  }

  public function validate($file, $options = array())
  {
    if (empty($file)) {
      $this->errors[] = 'Can not read the file you uploaded';
      return false;
    }

    if ($error = $this->checkFileUploadError($file)) {
      $this->error[] = $error;
      return false;
    }

    $this->setData($file);

    $this->renameIfTooLong = (isset($options['rename'])) ? $options['rename'] : false;

    if ($error = $this->checkMimeBinaries()) {
      $this->errors[] = $error;
    }

    if ($error = $this->checkNameLength()) {
      if ($this->renameIfTooLong) {
        $this->fileName = $this->setNewName('too long');
      } else {
        $this->errors[] = $error;
      }
    }

    if ($error = $this->checkSize()) {
      $this->errors[] = $error;
    }

    if (empty($this->errors)) {
      return true;
    }
    
    return false;
  }

  public function setData($file)
  {
    $fileInfo = new finfo(FILEINFO_MIME);

    $this->fileName = $file['name'];
    $this->fileSize = $file['size'];
    $this->filePath = $file['tmp_name'];

    $this->fileData = file_get_contents($this->filePath);
  }

  public function setDestination($destination)
  {
    $this->destination = $destination;
  }

  public function setPrefixIdentifier($prefixIdentifier)
  {
    $this->prefixIdentifier = $prefixIdentifier;
  }

  public function setRestriction($nameLength, $maxSize, $typesArray = null)
  {
    $this->setNameLimit($nameLength);
    $this->setSizeLimit($maxSize);
    $this->setAllowedFileTypes($typesArray);
  }

  public function setNewName($case = null)
  {
    if ($case === 'too long') {
      $newName = substr(pathinfo($this->fileName, PATHINFO_FILENAME), 0, 10) . pathinfo($this->fileName, PATHINFO_EXTENSIONS);
      return $newName;
    }

    $prefix = $this->prefixIdentifier . date('Ymd') . time();
    $newName = uniqid($prefix , true) . '.' . pathinfo($this->fileName, PATHINFO_EXTENSION);
    return $newName;
  }

  public function setAllowedFileTypes($typesArray = null)
  {
    if (empty($typesArray)) {
      array_push($this->allowedMimesBinary, $this->mimesBinary['all']);
      return;
    }

    foreach ($typesArray as $type) {
      if (in_array($type, array_keys($this->mimesBinary))) {
        $this->allowedMimesBinary[$type] = $this->mimesBinary[$type];
      }
    }
  }

  public function setNameLimit($nameLength)
  {
    $this->nameLimit = $nameLength;
  }

  public function setSizeLimit($maxSize)
  {
    $this->maxSize = $maxSize;
  }

  public function checkNameLength()
  {
    if (strlen($this->fileName) > $this->nameLimit)  {
      return "File name is too long. Must be under {$this->nameLimit}";
    }
  }

  public function checkSize()
  {
    if ($this->fileSize > $this->maxSize) {
      $maxSizeInMb = $this->maxSize / 1000000;
      return "File size must not exceed {$maxSizeInMb} mb";
    }
  }

  public function checkMimeBinaries()
  {
    $match = 0;

    foreach ($this->allowedMimesBinary as $type) {
      if (strncmp($type, $this->fileData, strlen($type)) === 0) {
        $match = 1;
        break;
      }
    }

    if ($match === 0) {
      $mimeList = implode(', ', array_keys($this->allowedMimesBinary));
      return (isset($this->replacedErrMsg[__FUNCTION__])) ? $this->replacedErrMsg[__FUNCTION__] : "File type must be {$mimeList}";
    }
  }

  public function checkFileUploadError($file)
  {
    if (!empty($file['error'])) {
      switch ($file['error']) {
      case  0:
        return null;
        break;
      case 1:
        return 'File size exceeds upload_max_filesize configuration';
        break;
      case 2:
        return 'File size exceeds MAX_FILE_SIZE in html HTML';
        break;
      case 3:
        return 'File partially uploaded';
        break;
      case 4:
        return 'No file was uploaded';
        break;
      case 6:
        return 'Missing temporary folder';
        break;
      case 7:
        return 'Failed to write disk';
        break;
      case 8:
        return 'A PHP Extension stopped the file upload';
        break;
      default:
        return 'There was an error with the file upload';
        break;
      }
    }

    return null;
  }

  public function getErrors()
  {
    return $this->errors;
  }

  public function replaceErrorMessage($method, $msg)
  {
    $this->replacedErrMsg[$method] = $msg;
  }
}
