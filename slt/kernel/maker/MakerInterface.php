<?php

namespace Kernel\Maker;

interface MakerInterface{
	/**
	 * Проверяет существует ли файл миграции.
	 *
	 * @method migration_exists
	 *
	 * @param  string $migration_name Название миграции.
	 * @param  string $path_to_migration_dir Путь отличный от дефолтного к папке с миграциями.
	 *
	 * @return boolean Существует или нет.
	 */
	public static function migration_exists($migration_name, $path_to_migration_dir = null);

	/**
	 * Поднять миграцию, то есть установить её в бд.
	 *
	 * @method migration_up
	 *
	 * @param  string $migration_name Название миграции.
	 * @param  string $path_to_migration_dir Путь отличный от дефолтного к папке с миграциями.
	 * @param  boolean $with_model Указывает, нужно ли автоматически создать файл модели для этой миграции.
	 *
	 * @return boolean
	 */
	public static function migration_up($migration_name, $path_to_migration_dir = null, $with_model = false);

	/**
	 * Удалить ранее установленную миграцию.
	 *
	 * @method migration_down
	 *
	 * @param  string $migration_name Название миграции.
	 * @param  string $path_to_migration_dir Путь отличный от дефолтного к папке с миграциями.
	 * @param  boolean $with_model Указывает, нужно ли автоматически удалить файл модели (на самом неде удаление не происходит, файл будет просто помечен как удалённый).
	 *
	 * @return boolean
	 */
	public static function migration_down($migration_name, $path_to_migration_dir = null, $with_model = false);

	/**
	 * Удаляет ранее установленную миграцию и устанавливает её заново.
	 *
	 * @method migration_refresh
	 *
	 * @param  string $migration_name Название миграции.
	 * @param  string $path_to_migration_dir Путь отличный от дефолтного к папке с миграциями.
	 * @param  boolean $with_model Указывает, нужно ли автоматически пересоздать файл модели.
	 *
	 * @return boolean
	 */
	public static function migration_refresh($migration_name, $path_to_migration_dir = null, $with_model = false);

	/**
	 * Поднять, тобишь установить, все миграции о которых знает фреймворк.
	 *
	 * @method migration_up_all
	 *
	 * @param  string $path_to_migration_dir Путь отличный от дефолтного к папке с миграциями.
	 * @param  boolean $with_models Указывает, нужно ли автоматически создать файл модели для этой миграции.
	 *
	 * @return boolean
	 */
	public static function migration_up_all($path_to_migration_dir = null, $with_models = false);

	/**
	 * Деисталировать, тобишь удалить, все миграции о которых знает фреймворк.
	 *
	 * @method migration_down_all
	 *
	 * @param  string $path_to_migration_dir Путь отличный от дефолтного к папке с миграциями.
	 * @param  boolean $with_models Указывает, нужно ли автоматически удалить файл модели (на самом неде удаление не происходит, файл будет просто помечен как удалённый).
	 *
	 * @return boolean
	 */
	public static function migration_down_all($path_to_migration_dir = null, $with_models = false);

	/**
	 * Удаляет ранее установленные миграции, о которых известно фреймворку, и устанавливает их заново.
	 *
	 * @method migration_refresh_all
	 *
	 * @param  string $path_to_migration_dir Путь отличный от дефолтного к папке с миграциями.
	 * @param  boolean $with_models Нужно ли обновить файлы моделей.
	 *
	 * @return boolean
	 */
	public static function migration_refresh_all($path_to_migration_dir = null, $with_models = false);

	/**
	 * Получить список файлов миграций о которых известно фреймворку.
	 *
	 * @method migrations_list
	 *
	 * @param  string $path_to_migration_dir Путь отличный от дефолтного к папке с миграциями.
	 *
	 * @return array Список миграций с полными путями к ним.
	 */
	public static function migrations_list($path_to_migration_dir = null);
}