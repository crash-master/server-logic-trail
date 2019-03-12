<?php

namespace Kernel\Cache;

interface CacheInterface{
	/**
	 * Для получения пути к файлу с кешем
	 *
	 * @method get_path_to_cache_file
	 *
	 * @param  string $name Имя файла с кешем, если кеш хранится в файлах, к оторому нужно получить путь, расширение указывать не нужно.
	 *
	 * @return string Полный путь к файлу, если кеш хранится в файлах.
	 */
	public static function get_path_to_cache_file($name);

	/**
	 * Метод для авто очиски не актуальных, устаревших данных кеша
	 *
	 * @method autoclear_not_relevant_cache
	 *
	 * @return int Возвращается количество удалённых данных кеша.
	 */
	public static function autoclear_not_relevant_cache();

	/**
	 * Добавления нового кеша в ручном режиме.
	 *
	 * @method set
	 *
	 * @param  string $name Название одновременно является псевдонимом.
	 * @param  $cache_data Любые сериализируемые данные.
	 */
	public static function set($name, $cache_data);

	/**
	 * Проверка кеш файла на существование по имени.
	 *
	 * @method exists
	 *
	 * @param  string $name Имя кеша.
	 *
	 * @return boolean Существует или нет.
	 */
	public static function exists($name);

	/**
	 * Получение десериализированых данных их кеша по имени.
	 *
	 * @method get
	 *
	 * @param  string $name Название кеша.
	 *
	 * @return Десериализированные данные.
	 */
	public static function get($name);

	/**
	 * Удаление существующего кеша по названию.
	 *
	 * @method remove
	 *
	 * @param string $name Название кеша.
	 *
	 * @return boolean Прошло ли удачно удаление.
	 */
	public static function remove($name);

	/**
	 * Конструкция для удобного кеширования результата выполнения кода.
	 *
	 * @method code
	 *
	 * @param  string $name Будущее название кеша, если он будет создан.
	 * @param  function $code_in_func Анонимная функция с кодом, который должен выполнится, кешироватся будет тот результат, который возвращает эта функция.
	 *
	 * @return Возвращает результат выполнения кода, реальный или закешированный.
	 */
	public static function code($name, $code_in_func);
}