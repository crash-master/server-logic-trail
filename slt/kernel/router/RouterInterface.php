<?php

namespace Kernel\Router;

/**
 * RouterInterface показывает доступный для внешнего использования функционал класса Router, 
 * который в свою очередь используется для веб маршрутизации
 */

interface RouterInterface{
	/**
	 * Резервирование маршрута и связываение его с обработчиком (контроллером)
	 * Работает с uri, не смотря на название, не учитывает данные переданные методом get
	 *
	 * @method get
	 *
	 * @param  string $route Маршрут указанный в строке, должен обязательно начинатся с "/"
	 * @param  string $action Обработчик маршрута, может указыватся в виде анонимной функции без параметров или в виде строки формата "MyController@myAction"
	 *
	 * @return void 
	 */
	public static function get($route, $action);

	/**
	 * Маршрутизация метода передачи данных post. 
	 *
	 * @method post
	 *
	 * @param  string $post Название переменной, в массиве $_POST при наличии которой, будет вызван соответствующий обработчик
	 * @param  string $action Обработчик в виде строки в формате "MyController@myAction"
	 * @param  string $uri Не обязательный параметр, который указывает на дополнительно условие активации обработчика, имеет вид "/post/new/aticle"
	 *
	 * @return void
	 */
	public static function post($post, $action, $uri = null);

	/**
	 * Обёртка над методом этого класса get(), автоматически формирует маршрут на основе указанного обработчика.
	 * Например Router::action("MyController@some_method") сформирует маршрут "/my/some-method" 
	 * и привяжет его у обработчику "MyController@some_method".
	 *
	 * @method action
	 *
	 * @param  string $action Обработчик в виде строки, например "MyController@some_method".
	 *
	 * @return void
	 */
	public static function action($action);

	/**
	 * Обёртка над методом этого же класса action(). Позволяет сгенерировать множество маршрутов для указанных обработчиков
	 *
	 * @method actions
	 *
	 * @param  array $actions_list Массив строк с указанием обработчиков, которым нужно сгенерировать маршруты
	 *
	 * @return void
	 */
	public static function actions($actions_list);

	/**
	 * Обёртка над методом actions, позволяет автоматически сгенерировать маршруты для указанного класса контроллера (обработчиков)
	 *
	 * @method controller
	 *
	 * @param  string $classname Название класса "IndexController"
	 * @param  array $without Не обязательный параметр, массив название методов, для который не нужно генерировать маршруты
	 *
	 * @return void
	 */
	public static function controller($classname, $without = []);

	/**
	 * Возвращает список существующих маршрутов.
	 *
	 * @method getRouteList
	 *
	 * @return array Массив маршрутов.
	 */
	public static function getRouteList();

	/**
	 * Метод для установки обработчика страницы 404
	 *
	 * @method _404
	 *
	 * @param  string $action Обработчик страницы 404
	 *
	 * @return void
	 */
	public static function _404($action);

	/**
	 * Находит и возвращает маршрут для указанного обработчика
	 *
	 * @method urlto
	 *
	 * @param  string $action_name Обработчик в виде строки, например "IndexController@welcome_page"
	 * @param  array $params оссоциативный масив параметров со значениями, которые требует обработчик на вход
	 *
	 * @return string Маршрут к обработчику, типа "/example/page"
	 */
	public static function urlto($action_name, $params = null);
}