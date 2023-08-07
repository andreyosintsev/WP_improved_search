# search_by_title()
## Улучшенный поиск для WordPress

  <b>Автор: Андрей Осинцев</b>
  https://github.com/andreyosintsev/

  Представленная функция позволяет производить поиск статьи в CMS WordPress на основе введённых пользователем поисковых запросов.
  Оригинальный поиск в WordPress позволяет искать лишь по всей строке целиком.
  Данный вариант разбивает строку на отдельные слова, исключает стоп-слова, отрезает конец слов для поиска по словоформам.
  Каждому подходящему варианту назначается весовой коэффициент схожести. Чем больше слов в заголове статьи совпадает со словами из запроса,
  тем больший коэффициент присваивается этой статье. Результат сортируется по убыванию коэффициента схожести.

  Функция возвращает массив ID ($post->ID) найденных статей, отсортированный по убыванию схожести.

  ### Использование функции:
	1) Разместить функцию search_by_title() в общем файле функций темы WordPress functions.php
	2) В файле вывода результатов поиска темы, обычно search.php, добавить:
	   	
     	$searchstring = get_search_query();
		$ids = search_by_title( get_search_query() );

  $ids - это массив ID ($post->ID) с найденными статьями, пригодный для дальнейшей работы.

## An improved search for WordPress

  <b>Author: Andrey Osintsev</b>
  https://github.com/andreyosintsev/

  The presented function allows you to search for an article in CMS WordPress based on the search queries entered by the user.
  The original search in WordPress allows you to search only the entire string.
  This function breaks the string into individual words, eliminates stop words, and cuts off the end of words for word form searches.
  Each matching option is assigned a similarity weighting factor. The more words in the header of an article match the words in the query,
  the higher the coefficient is assigned to that article. The result is sorted by decreasing similarity coefficient.

  The function returns an array of IDs ($post->ID) of the found articles sorted by decreasing similarity coefficient.

  ### Function usage:
	1) Put the search_by_title() function in the general WordPress theme functions.php file
	2) Add to the theme's search results output file search.php:

		$searchstring = get_search_query();
		$ids = search_by_title( get_search_query() );

  $ids is an array of IDs ($post->ID) with found articles, suitable for further work.
