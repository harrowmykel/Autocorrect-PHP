<?php 

/**
 * @author Aro Micheal micheal.piccmaq.com.ng
 * 
 * @date 04 Sept.2019
 * 
 */


/**
 * Class for automatic correct.
 */
class AutoCorrect{

	var $path_to_file = __DIR__ . "\words.txt";
	var $file_content_as_string = "";
	var $file_content_as_array = [];
	var $file_content_as_object = [];	
	var $alphabet = "abcdefghijklmnopqrstuvwxyzöüßä";
	var $alphabet_as_array = [];


	/**
	 * Constructs the object.
	 *
	 * @param      string  $path_to_custom_file  The path to custom autocorrect file
	 */
	public function __construct($path_to_custom_file = ""){
		$this->path_to_file = empty($path_to_custom_file)?$this->path_to_file : $path_to_custom_file;
		$this->file_content_as_string = file_get_contents($this->path_to_file);

		//convert to lowercase
		$this->file_content_as_string = strtolower($this->file_content_as_string);
		$this->file_content_as_array = explode(" ", $this->file_content_as_string);
		$this->alphabet_as_array = str_split($this->alphabet);

		//convert to an object of counts
		foreach ($this->file_content_as_array as $key => $value) {
			if(isset($this->file_content_as_object["$value"])){
				//best readibility
				$this->file_content_as_object["$value"]++;
			}else{
				$this->file_content_as_object["$value"] = 1;				
			}
		}
	}

	/**
	 * Gets the word count.
	 *
	 * @param      <type>  $word   The word
	 */
	public function getWordCount($word){
		return isset($this->file_content_as_object["$word"])?$this->file_content_as_object["$word"]:0;
	}

	/*
	  Returns an object with each unique word in the input as a key,
	  and the count of the number of occurances of that word as the value.
	*/
	public function getWordsAsObject(){
		return $this->file_content_as_object;
	}

	/*
	  Returns the set of all strings 1 edit distance away from the input word.
	  This consists of all strings that can be created by:
	    - Adding any one character (from the alphabet) anywhere in the word.
	    - Removing any one character from the word.
	    - Transposing (switching) the order of any two adjacent characters in a word.
	    - Substituting any character in the word with another character.
	*/
	public function editDistance($word){
		$words = str_split($word);
		// add, delete, transpose, substitute
		$alphabet_array = $this->alphabet_as_array;
		$result = [];

		//Adding any one character (from the alphabet) anywhere in the word.
		//azebra, zaebra,a
		foreach ($words as $wkey => $word_character) {
			foreach ($alphabet_array as $key => $alphabet_character) {	
				$newWord = array_slice($words, 0);
				array_splice($newWord, $wkey, 0, $alphabet_character);
				$result[] = implode("", $newWord);
			}
		}

		//Removing any one character from the word.
		// ebra, zbra, zera
		if(count($words) > 1){
			foreach ($words as $wkey => $word_character) {
				$newWord = array_slice($words, 0);
				array_splice($newWord, $wkey, 1);
				$result[] = implode("", $newWord);
			}
		}

		//Transposing (switching) the order of any two adjacent characters in a word.
		// zebra, ezbra, ebzra
		if(count($words) > 1){
			foreach ($words as $wkey => $word_character) {
				$newWord = array_slice($words, 0);
				$rmv = array_slice($newWord, $wkey, 1);
				array_splice($newWord, $wkey+1, 0, $rmv[0]);
				$result[] = implode("", $newWord);
			}
		}

		//Substituting any character in the word with another character.
		// aebra, zbbra, zecra
		foreach ($words as $wkey => $word_character) {
			foreach ($alphabet_array as $key => $alphabet_character) {	
				$newWord = array_slice($words, 0);
				$newWord[$wkey] = $alphabet_character;
				$result[] = implode("", $newWord);
			}
		}

		return $result;
	}


	/* Given a word, attempts to correct the spelling of that word.
	  - First, if the word is a known word, return the word.
	  - Second, if the word has any known words edit-distance 1 away, return the one with
	    the highest frequency, as recorded in NWORDS.
	  - Third, if the word has any known words edit-distance 2 away, return the one with
	    the highest frequency, as recorded in NWORDS. (HINT: what does applying
	    "editDistance1" *again* to each word of its own output do?)
	  - Finally, if no good replacements are found, return the word.
	*/
	public function correct($word_to_be_corrected){
		if($this->getWordCount($word_to_be_corrected) > 0){
			return $word_to_be_corrected;
		}
		$maxCount = 0;
		$correctWord = $word_to_be_corrected;
		$editDistance1Words = $this->editDistance($word_to_be_corrected);
		$editDistance2Words = [];

		foreach ($editDistance1Words as $key => $editDistance1Word) {
			$editDistance2Words = array_merge($editDistance1Words, $this->editDistance($editDistance1Word));
			//this part is originally included in another for loop of editDistance1Words in the js version
			if(isset($this->file_content_as_object[$editDistance1Word])){
				if($this->file_content_as_object[$editDistance1Word] > $maxCount){
					$maxCount = $this->file_content_as_object[$editDistance1Word];
					$correctWord = $editDistance1Word;
				}
			}
		}

		//======================================================================== 

		$maxCount2 = 0;
		$correctWord2 = $correctWord;

		foreach ($editDistance2Words as $key => $editDistance2Word) {
			if(isset($this->file_content_as_object[$editDistance2Word])){
				if($this->file_content_as_object[$editDistance2Word] > $maxCount2){
					$maxCount2 = $this->file_content_as_object[$editDistance2Word];
					$correctWord2 = $editDistance2Word;
				}
			}
		}

		if (strlen($word_to_be_corrected) < 6) {
			if($maxCount2 > 100*$maxCount){
			  return $correctWord2;
			}
			return $correctWord;  
		}else {
			if($maxCount2 > 4*$maxCount){
			  return $correctWord2;
			}
			return $correctWord;  
		};
	}

}

?>