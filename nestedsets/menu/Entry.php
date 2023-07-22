<?php
namespace menu;

class Entry {
    protected
        /**
         * id объекта
         * @var int
         */
        $id = 0,
        
        /**
         * Наименование узла для человека
         * @var string
         */
        $title = '',
        
        /**
         * Имя узла для URL
         * @var string
         */
        $name = '',
        
        /**
         * Индекс слева
         * @var int
         */
        $leftIndex = 0,
        
        /**
         * Индекс справа
         * @var int
         */
        $rightIndex = 0;
    
    public function GetId( ) {
        return $this->id;
    }
    
    public function SetId( $id ) {
        $this->id = $id;
    }
    
    public function GetTitle( ) {
        return $this->title;
    }
    
    public function SetTitle( $title ) {
        $this->title = $title;
    }
    
    public function GetName( ) {
        return $this->name;
    }
    
    public function SetName( $name ) {
        $this->name = $name;
    }
    
    public function GetLeftIndex( ) {
        return $this->leftIndex;
    }
    
    public function SetLeftIndex( $leftIndex ) {
        $this->leftIndex = $leftIndex;
    }
    
    public function GetRightIndex( ) {
        return $this->rightIndex;
    }
    
    public function SetRightIndex( $rightIndex ) {
        $this->rightIndex = $rightIndex;
    }
}
