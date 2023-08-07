<?php
/*
  Улучшенный поиск для WordPress

  Автор: Андрей Осинцев
  https://github.com/andreyosintsev/

  Представленная функция позволяет производить поиск статьи в CMS WordPress на основе введённых пользователем поисковых запросов.
  Оригинальный поиск в WordPress позволяет искать лишь по всей строке целиком.
  Данный вариант разбивает строку на отдельные слова, исключает стоп-слова, отрезает конец слов для поиска по словоформам.
  Каждому подходящему варианту назначается весовой коэффициент схожести. Чем больше слов в заголове статьи совпадает со словами из запроса,
  тем больший коэффициент присваивается этой статье. Результат сортируется по убыванию коэффициента схожести.

  Функция возвращает массив ID ($post->ID) найденных статей, отсортированный по убыванию схожести

  Использование функции:
	1) Разместить функцию search_by_title() в общем файле функций темы WordPress functions.php
	2) В файле вывода результатов поиска темы, обычно search.php, добавить:
 
  $searchstring = get_search_query();
  $ids = search_by_title( get_search_query() );

  $ids - это массив ID ($post->ID) с найденными статьями, пригодный для дальнейшей работы.
*/

function search_by_title ($search='') {
	global $wpdb;
	
	if (empty($search)) return;

	mb_internal_encoding("UTF-8"); 

	//Удаление спецсимволов и пробелов в начале и конце строки
	$search = stripslashes($search);
	$search = htmlspecialchars($search);
	$search = trim($search);

	//Ограничим поиск строкой 255 символов и приведем к нижнему регистру
	$search = mb_substr($search, 0, 255);
	$search = mb_strtolower($search);	

	//Разобъём на отдельные слова
	$search_words = mb_split("[ ,]+", $search, 10);

	//Массив стоп-слов, не участвующих в поиске
	$questions = ['где', 'когда', 'куда', 'откуда', 'почему', 'зачем', 'как'];

	$filtered_words = array();

	//Отфильтруем стоп-слова и слова короче 4 символов
	foreach ($search_words as $word) {
		if (!in_array($word, $questions) && (mb_strlen($word) >= 4)) {
			array_push($filtered_words, $word);
		}
	}

	//Запрос к БД WordPress. Таблицы и поля стандартные для WordPress:
	// posts - таблица записей (постов)
	// post_status - статус поста (publish - опубликованный)
	// post_type - тип записи (post - запись, не изображение и не вложение)
	// post_title - заголовок записи

	$sql = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_title LIKE '%%$search%%'", $search));

	$result = array();

	//Если совпала вся строка - присвоим коэффициент схожести 10
	foreach ($sql as $s) {
		$result[$s] = 10;
	}

	//Ищем отдельно по каждому слову
	foreach ($filtered_words as $word){
		
	  //Обрежем слово с конца для поиска словоформ
	  //Подобрано опытным путём
	  //Если слово 4 символа - не обрезаем, 5 символов - обрезаем 1 символ с конца, 6 символов и более - 2 символа с конца

		if (mb_strlen($word) >= 6) $word = mb_substr($word, 0, mb_strlen($word) - 2);
		else if (mb_strlen($word) == 5) $word = mb_substr($word, 0, mb_strlen($word) - 1);

		
		$sql = $wpdb->get_col($wpdb->prepare("SELECT DISTINCT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND post_title LIKE '%%$word%%'", $word));
		
		//Для каждой записи с найденным словом прибавляем к коэффициенту схожести 2
		foreach ($sql as $s) {
			if (empty($result[$s])) $result[$s] = 2; else $result[$s] += 2;
		}
	}

	//Сортируем по коэффициентам схожести по убыванию.
	arsort ($result);

	$result_total = array();

	foreach ($result as $id=>$weight) {
		$result_total[] = $id;
	}

	return $result_total;
}
