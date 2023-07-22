<?php
namespace menu\tree;

use menu\Entry;

class Node {
    protected
        /**
         * Родительский узел
         * @var Node
         */
        $parent = null,
        
        /**
         * Данные узла
         * @var \menu\Entry
         */
        $entry = null,
        
        /**
         * Потомки
         * @var Node[]
         */
        $children = [ ];
    
    public function __construct( Entry $entry, Node $parent = null ) {
        $this->parent = $parent;
        $this->entry = $entry;
    }
    
    /**
     * @return \menu\tree\Node
     */
    public function GetParent( ) {
        return $this->parent;
    }
    
    public function SetParent( Node $parent ) {
        $this->parent = $parent;
    }

    /**
     * @return \menu\Entry
     */
    public function GetEntry( ) {
        return $this->entry;
    }
    
    /**
     * @return array
     */
    public function GetChildren( ) {
        return $this->children;
    }
    
    public function AddChild( Node $node ) {
        $this->children[ ] = $node;
    }
}
