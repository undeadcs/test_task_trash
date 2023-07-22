<?php
namespace menu;

use menu\tree\Node;

/**
 * Модель дерева для построения из массива
 */
class Tree {
    protected
        /**
         * Корень дерева
         * @var Node
         */
        $root = null,
    
        /**
         * @var \SplObjectStorage
         */
        $nodes = null;
    
    public function __construct( ) {
        $entry = new Entry;
        $entry->SetTitle( '' );
        $entry->SetName( '' );
        $entry->SetLeftIndex( 1 );
        $entry->SetRightIndex( 2 );
        
        $this->root = new Node( $entry );
        
        $this->nodes = new \SplObjectStorage;
        $this->nodes->attach( $this->root );
    }
    
    public function GetRoot( ) {
        return $this->root;
    }
    
    public function GetNodes( ) {
        return $this->nodes;
    }
    
    /**
     * Импорт из JSON
     * @param object $json
     */
    public function ImportFromJson( $json ) {
        /*
         * на первом шаге для каждого элемента массива создается узел
         * с родителем в виде корня
         * далее каждый узел обрабатывается функцией:
         * создать узел из текущего объекта
         * обработать каждый дочерний элемент
         */
        foreach( $json as $child ) {
            $this->ProcessJsonNode( $this->root, $child );
        }
    }
    
    protected function ProcessJsonNode( Node $parent, $json ) {
        $node = $this->CreateNodeFromJson( $parent, $json );
        $this->nodes->attach( $node );
        $parent->AddChild( $node );
        // хоть и придирка, но: children - это уже множественное число, добавлять суффикс "s" не нужно
        $children = isset( $json->childrens ) ? ( array ) $json->childrens : ( isset( $json->{ '0' } ) ? ( array ) $json->{ '0' } : [ ] );
        foreach( $children as $child ) {
            $this->ProcessJsonNode( $node, $child );
        }
    }
    
    protected function CreateNodeFromJson( Node $parent, $obj ) {
        $entry = new Entry;
        $entry->SetTitle( $obj->name );
        $entry->SetName( $obj->alias );
        
        return new Node( $entry, $parent );
    }
    
    /**
     * Построение индексов вложенных множеств
     */
    public function BuildNestedSet( ) {
        $this->PreorderTraversal( $this->root, 1 );
    }
    
    /**
     * Симметричный обход дерева
     */
    protected function PreorderTraversal( Node $node, $index ) {
        $entry = $node->GetEntry( );
        $entry->SetLeftIndex( $index );
        
        foreach( $node->GetChildren( ) as $child ) {
            $index = $this->PreorderTraversal( $child, $index + 1 );
        }
        
        $ret = $index + 1;
        $entry->SetRightIndex( $ret );
        
        return $ret;
    }
}
