<?php
return [
	'api/register' => [
		'controller' => 'conference',
		'action' => 'register',
	],
	'api/details' => [
		'controller' => 'conference',
		'action' => 'details',
	],
	'api/members/count' => [
		'controller' => 'conference',
		'action' => 'membersCount',
	],
	'api/members' => [
		'controller' => 'conference',
		'action' => 'members',
	],
	'api/update' => [
		'controller' => 'conference',
		'action' => 'update',
	],
	'api/user/personal/.*' => [
		'controller' => 'conference',
		'action' => 'personal',
	],
	'api/user/details/.*' => [
		'controller' => 'conference',
		'action' => 'details',
	],
	'img/.*' => [
		'controller' => 'conference',
		'action' => 'img',
	],
	'' => [
		'controller' => 'conference',
		'action' => 'index',
	],
];