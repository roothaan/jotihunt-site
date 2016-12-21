<?php

class Image {
    private $id;
    private $data;
    private $name;
    private $extension;
    private $sha1;
    private $file_size;
    private $last_modified;

    public function __construct() {
    }

    public function getId(){
        return $this->id;
    }

    public function setId($id){
        $this->id = $id;
    }

    public function getData(){
        return $this->data;
    }
    
    public function getEncodedData() {
        return pg_escape_bytea($this->data);
    }

    public function setData($data){
        $this->data = $data;
    }

    public function getName(){
        return $this->name;
    }

    public function setName($name){
        $this->name = $name;
    }

    public function getExtension(){
        return $this->extension;
    }

    public function setExtension($extension){
        $this->extension = $extension;
    }

    public function getSha1(){
        return $this->sha1;
    }

    public function setSha1($sha1){
        $this->sha1 = $sha1;
    }

    public function getFileSize(){
        return $this->file_size;
    }

    public function setFileSize($file_size){
        $this->file_size = $file_size;
    }

    public function getLastModified(){
        return $this->last_modified;
    }

    public function setLastModified($last_modified){
        $this->last_modified = $last_modified;
    }
    
    public function toArray() {
        return array(
            'id' => $this->getId(),
            'data' => $this->getData(),
            'name' => $this->getName(),
            'extension' => $this->getExtension(),
            'sha1' => $this->getSha1(),
            'file_size' => $this->getFileSize(),
            'last_modified' => $this->getLastModified(),
            );
    }
    
    public function addValuesfromArray(array $imageArray) {
        if(isset($imageArray['id'])) {
            $this->setId($imageArray['id']);
        }
        if(isset($imageArray['data'])) {
            $this->setData($imageArray['data']);
        }
        if(isset($imageArray['name'])) {
            $this->setName($imageArray['name']);
        }
        if(isset($imageArray['extension'])) {
            $this->setExtension($imageArray['extension']);
        }
        if(isset($imageArray['sha1'])) {
            $this->setSha1($imageArray['sha1']);
        }
        if(isset($imageArray['file_size'])) {
            $this->setFileSize($imageArray['file_size']);
        }
        if(isset($imageArray['last_modified'])) {
            $this->setLastModified($imageArray['last_modified']);
        }
    }
}