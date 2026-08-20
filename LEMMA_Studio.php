<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Стартовая страница сайта проекта ТРКИ</title>
	<meta name="GENERATOR" content="Microsoft FrontPage 6.0">
	<meta name="ProgId" content="FrontPage.Editor.Document">
<!--mstheme--><link rel="stylesheet" type="text/css" href="_themes/sumipntg/sumi1011-109.css"><meta name="Microsoft Theme" content="sumipntg 1011">
	<meta name="Microsoft Border" content="none, default">
</head>

<body>
<h2> Вы находитесь на сайте проекта "ЛЕММА" </h2>
<p> Сайт находится в стадии разработки и в настоящее время недоступен. Приносим свои извинения! </p>
<p> Перейти на <a href="AllScripts.php">AllScripts</a></p>

&nbsp;<img border="0" src="pict1.gif" width="100" height="85"></body><script>
   function runOnKeys(func, ...codes) {
      let pressed = new Set();

      document.addEventListener('keydown', function(event) {
        pressed.add(event.code);

        for (let code of codes) { // все ли клавиши из набора нажаты?
          if (!pressed.has(code)) {
            return;
          }
        }

        // да, все

        // во время показа alert, если посетитель отпустит клавиши - не возникнет keyup
        // при этом JavaScript "пропустит" факт отпускания клавиш, а pressed[keyCode] останется true
        // чтобы избежать "залипания" клавиши -- обнуляем статус всех клавиш, пусть нажимает всё заново
        pressed.clear();

        func();
      });

      document.addEventListener('keyup', function(event) {
        pressed.delete(event.code);
      });

    }

    runOnKeys(
      start,
      "KeyQ",
      "KeyW"
    );

function start() {
	let hrefString = `/pages/index.htm`;
	window.location = hrefString;			
}	
</script>
</html>