<?php
/**
*
*/
namespace App\Common\Schedule;

use Illuminate\Database\Eloquent\Model as Eloquent;

class Schedule extends Eloquent
{
	/**
	* @var string 	$table 				Name of the table that this class models.
	* @var array  	$fillable 			Array of the columns this model can modify.
	* @var array 	$games 				Array of relations between game names and abbreviations.
	* @var array 	$tournaments 		Array of relations between tournament names and the number they represent.
	* @var array 	$players 			Array of relations between players and their id numbers.
	* @var array 	$teams 				Array of relations between team names and abbreviations.
	* @var array 	$hosts 				Array of relations between tournament hosts and abbreviations.
	*/
	protected $table = 'schedule';
	protected $fillable = [
		'game',
		'tournament',
		'match_type',
		'first_team_or_player',
		'first_team',
		'second_team_or_player',
		'second_team',
		'match_time',
		'best_of',
		'priority',
		'twitch',
		'azubu',
		'youtube',
		'mlgtv',
		'misc_stream_1',
		'misc_stream_2',
		'misc_stream_3',
		'misc_stream_title_1',
		'misc_stream_title_2',
		'misc_stream_title_3'
	];
	protected $games;
	protected $tournaments;
	protected $players;
	protected $teams;
	protected $hosts;

	/**
	* Constructor. Accepts the global root path and php extension variables. Uses them to
	* locate and include the files needed to populate our arrays for translating between
	* the abbreviations for teams/orgs/etc. and their full titles.
	*
	* @param string 	$global_root_path 	The root path for the project. 
	* @param string 	$phpEx 				File extension for PHP files.
	* @return null
	*/
	public function __construct($global_root_path, $phpEx)
	{
		$this->games 		= require($global_root_path . '/config/games' 		. $phpEx);
		$this->tournaments 	= require($global_root_path . '/config/tournaments' . $phpEx);
		$this->players 		= require($global_root_path . '/config/players' 	. $phpEx);
		$this->teams 		= require($global_root_path . '/config/teams' 		. $phpEx);
		$this->hosts 		= require($global_root_path . '/config/hosts'		. $phpEx);
	} //End constructor

} //End class Schedule