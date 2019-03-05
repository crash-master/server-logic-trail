<?php

namespace Kernel\Console;

/**
 * Консольный маршрутизатор
 */

interface ConsoleInterface{

	/**	
	 * Запуск маршрутизации
	 *
	 * @method routing
	 *
	 * @return void
	 */
	public static function routing();

	/**
	 * Добавлени связи маршрута с обработчиком
	 *
	 * @method route
	 * 
	 * @param  string $command описание команды (маршрута)
	 * @param  string $action Обработчик маршрута в виде "NameController@nameMethod"
	 *
	 * @return void
	 *
	 * @example route("my_little.command::$my_param1;$my_param2", "MyBigController@littleCommnd");
	 */
	public static function route($command, $action);

	/**
	 * Обработчик события "Такой команды (маршрута) не найдено"
	 *
	 * @method not_found
	 *
	 * @param  string $action Обработчик, представляет из себя строку с названием класса и названием метода класса, разделённого символом "@"
	 *
	 * @return void
	 *
	 * @example not_found("MyConsoleController@not_found_action");
	 */
	public static function not_found($action);

	/**
	 * Метод для автоматической генерации команды (маршрута) по имени указанного обработчика.
	 *
	 * @method action
	 *
	 * @param  string $action Обработчик в виде строки
	 *
	 * @return void
	 */
	public static function action($action);

	/**
	 * Получить карту консольных маршрутов (команд)
	 *
	 * @method get_routes_map
	 *
	 * @return array Возвращает ассоциативный массив с списком команд и их обработчиков.
	 */
	public static function get_routes_map();
}