<?php
/*
    My attempt at a Trie data structure
*/
class trie {

     public $root;

     public function __construct() {
          $this->root = new trieNode(); //Add a blank node as the head of the Trie
     }

    //Adds a word to the Trie
     public function addWord($word) {
          $charArray = str_split($word);

          $this->root->addLetters($charArray);
     }

    //Checks if $word is in the Trie, $word can be part of or a complete word ($completeWords)
     public function findWord($word, $completeWords) {
          $charArray = str_split($word);

          if($completeWords) {
               return $this->root->findLetters($charArray);
          }
          
          return $this->root->findLettersIncomplete($charArray);
     }

    //Finds all complete words that start with $prefix
     public function findAllMatches($prefix) {
          $charArray = str_split($prefix);
          $prefixNode = $this->root->hasPrefix($charArray);
          
          if($prefixNode !== FALSE) {
               $postfixArray = $prefixNode->getAllPostfixes('');
               if(!empty($postfixArray)) {
                    $returnArray = [];
                    foreach ($postfixArray as $key => $value) {
                         $returnArray[$key] = substr($prefix, 0, -1).$value; //have to sub the substr to prevent the $prefixNode's character from showing up twice
                    }
                    return $returnArray;
               }
          }
          return false;
     }

    //Print out all complete words 
     public function printAllWords() {
          $this->root->printAllWords('');
     }

    //Print out the entire data structure
     public function printAllData() {
          $this->root->printData(0);
     }
}

class trieNode {
     public $isFullWord = FALSE;
     public $children = [];
     public $character = '';

     public function __construct($character = '') {     
          $this->character = $character;
     }

    //Returns an array all complete words from $this point on
     public function getAllPostfixes($prefix) {
          static $listOfPostFix = [];
          if($this->isFullWord) {
               $listOfPostFix[] = $prefix.$this->character;
          }

          foreach ($this->children as $key => $child) {
               $child->getAllPostfixes($prefix.$this->character);
          }

          return $listOfPostFix;
     }

    //If $charArray is the begining of any word in the Trie return that node, otherwise False
     public function hasPrefix($charArray) {
          if(empty($charArray)) {
               return $this;
          }

          $firstChar = $charArray[0];
          $remnant = array_slice($charArray, 1);

          foreach ($this->children as $child) {
               if($child->character === $firstChar) {                    
                    return $child->hasPrefix($remnant);
               }               
          }

          return false;
     }

    //if $char array exsists anywhere in the Trie return true, otherwise false
     public function findLettersIncomplete($charArray) {
          static $found = false;

          if(empty($charArray)) {
               $found = true;
               return;
          }

          $firstChar = $charArray[0];
          
          foreach ($this->children as $child) {
               if($child->character === $firstChar) {
                    $charArray = array_slice($charArray, 1);
               }
               $child->findLettersIncomplete($charArray);
          }

          return $found;
     }

    //Checks if $charArray is a complete word in the Trie
     public function findLetters($charArray) {
          if(empty($charArray)) {
              if($this->isFullWord) {
                   return true;
               }
               else {
                   return false;
               }
          }
          

          $firstChar = $charArray[0];
          $remnant = array_slice($charArray, 1);

          foreach ($this->children as $child) {
               if($child->character === $firstChar) {                    
                    return $child->findLetters($remnant);
               }               
          }

          return false;
     }

    //Adds the letters in $charArray to the Trie
     public function addLetters($charArray) {
          if(!empty($charArray)) {
               $firstChar = $charArray[0];
               $remnant = array_slice($charArray, 1);

               $childNode = $this->getChild($firstChar);
               if($childNode === FALSE) {
                    $childNode = $this->addChild($firstChar);
               }
               $childNode->addLetters($remnant);          
          }
          else {
               $this->isFullWord = TRUE;
          }
     }

    //Checks if the current Node has any children === $char
     public function getChild($char) {
          foreach ($this->children as $key => $child) {
               if($char === $child->character) {
                    return $child;
               }
          }
          return FALSE;
     }

    //Adds a childNode to the $this
     public function addChild($char) {
          $child = new trieNode($char);
          $this->children[] = $child;
          return $child;
     }

    //Prints out the entire Trie
     public function printData($level = 0) {          
          for($i=0;$i<$level;$i++) {
               echo ":::";
          }
          echo print_r($this->character, true);          
          if($this->isFullWord) {
               echo "***";
          }
          echo '</br>';
          $level++;
          foreach ($this->children as $child) {
               $child->printData($level);               
          }
     }

    //Prints all complete words in the Trie
     public function printAllWords($prefix) {
          if($this->isFullWord) {
               echo $prefix.$this->character.'</br>';
          }

          if(!empty($this->children)) {
               foreach ($this->children as $key => $child) {
                    $child->printAllWords($prefix.$this->character);
               }
          }
     }
}

