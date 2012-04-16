© Honza Javorek

**DEVELOPMENT OF THIS PROJECT IS IDLE.**
Development of Joss doesn't continue anymore, but current version works fine. If you are interested in programming Joss, let me know.

**VÝVOJ TOHOTO PROJEKTU NEPOKRAČUJE.**
Vývoj Jossu nepokračuje, ale současná verze funguje dobře. Pokud máš zájem ve vývoji pokračovat, dej mi vědět.

----

- [Blog][blog]
- [Michal Janík o Jossu / Michal Janík about Joss][mj]


## Joss (en)
Joss is a simple web-based program, which allows you to **create and administrate your own website** in a very, very simple and easy way. Too intelligent people calls it CMS (content management system) or framework.

Joss believes if it is kind and friendly to others, they will too. Joss would like to be a really simple and fast CMS. At the same time it is not afraid of the **latest web technologies** and hopes it can restrain them to be useful and well implemented.

Code is written in **object PHP 5** and uses or plans to use technologies XML, XHTML, CSS, and JavaScript, in the most upstanding way.

Thanks to all who encouraged me to the creation of Joss by releasing their great projects and ideas as Open Source. Especially thanks to the genius [David Grudl][dg] and my friend [Michal Wiglasz][mw]. Joss derive benefits from containing a code of [Texy!][texy] formatter and some parts of [Nette framework][nette].

## Joss (cs)
Joss je jednoduchý webový program, díky němuž si můžeš velmi snadno **tvořit a spravovat své vlastní stránky**. Přehnaně inteligentní lidé tomu říkají framework, RS (redakční systém) nebo CMS (content management system, systém pro správu obsahu).

Joss si vyhrazuje právo všem okolo tykat. Věří, že když bude k druhým co nejvíce přátelský, bude mu opláceno stejně. Chtěl by být opravdu jednoduchým a rychlým redakčním systémem. Zároveň se nebojí **nejnovějších technologií** a doufá, že se mu podaří je zkrotit tak, aby byly prospěšné a implementovány správně.

Kód je napsán v **objektovém PHP 5** a využívá nebo hodlá využívat co nejpoctivěji technologií XML, XHTML, CSS a JavaScript.

Díky všem, kteří umožnili vznik Joss vydáním svých skvělých projektů a myšlenek jako Open Source. Především to byli génius [David Grudl][dg] a můj přítel [Michal Wiglasz][mw]. Projekt těží z toho, že využívá kód formátovače [Texy!][texy], některých částí [frameworku Nette][nette].



[blog]: http://blog.javorek.net/tag/joss
[mj]: http://www.michaljanik.cz/oblibene/joss
[dg]: http://www.davidgrudl.com/
[mw]: http://gringo.profitux.cz/
[texy]: http://texy.info/
[nette]: http://nette.org/



# Documentation (en)

© Jan Javorek, 2008

### License

This program is free software; you can redistribute it and/or modify it under the terms of the [GNU General Public License v2][gpl] as published by the Free Software Foundation.

### Requirements

#### Essential
- PHP 5 and higher
- possibility to write into directories
- possibility to use `.htaccess` files to change common server settings

#### Recommended
- `allow_url_fopen` set to true in `php.ini`
- `mod_rewrite`



# Dokumentace (cs)

© Jan Javorek, 2008

### Licence

Joss je svobodný program. Můžeš ho zdarma stáhnout, libovolně upravovat a používat ve svých aplikacích v souladu s podmínkami [GNU General Public License v2][gpl].

### Požadavky

#### Nutné
- PHP 5 a vyšší
- možnost zapisovat do některých adresářů
- možnost využívat soubory `.htaccess` a běžná nastavení serveru

#### Vhodné
- povolená direktiva `allow_url_fopen` v `php.ini` serveru
- `mod_rewrite`

### Obsah dokumentace

- Vlastnosti
- Instalace
- Struktura
- Syntaxe
- Zbytek

## Proč Joss?
Joss je _ultralight mezi frameworky_ a jednou možná i redakčními systémy v PHP. Jeho výhodou je to, že skoro nic neumí :) . Umí přesně jednu jednoduchou věc, ale pořádně. Nevystavíš nad ním sice tedy portál s fotogalerií, fórem a blogem, ale web o pěti stránkách s ním zvládneš sakramentsky rychle.

### Hlavní vlastnosti
Hlavní vlastnosti a rysy Jossu najdeš v [tomto článku na mém blogu][joss-intro].

- umožňuje **bleskové vytvoření malých a středních webů** bez klikací uživatelské administrace
- framework **lze pochopit asi za hodinu** čtení dokumentace a hrátek s kódem
- framework má **jen pár set kilobajtů**
- malé požadavky, instalace a konfigurace na **pět minut**, přesun na **tři minuty**
- pro psaní vlastního obsahu webu **nemusíš znát HTML** -- web je kompletně v Texy! souborech

### Podpora v jádře
- **hierarchická menu**
- více **jazykových verzí** webu
- cache zvyšuje **rychlost aplikace**
- obsahuje jednoduchý **RSS agregátor**
- **automatické** generování `sitemap.xml`, `robots.txt`, ...

### Na okraj
- Joss je **open source** (proč je zrovna toto výhoda, si přečti jinde :) )
- velmi jednoduchá tvorba **pluginů** (rozšíření syntaxe Texy!)

## Instalace

### Krok 1
Nakopíruj všechny soubory a složky tak jak jsou do místa, kde má být web. Poté, ještě před tím, než poprvé zobrazíš jakoukoliv stránku v prohlížeči, uprav soubor `config.ini` v adresáři `config`.

**Určitě není dobré to dělat to v opačném pořadí. Vlastně je to zcela nežádoucí.**

### Krok 2
Upravování `config.ini`:

#### Prostředí
- **modRewrite:** zda může Joss používat _mod\_rewrite_, bez něj vypadají adresy jako `index.php?doc=stránka`
- **debug:** do ostrého provozu nastavit na false, při ladění na true

#### Vlastní web
- **title:** název celého webu (lze vynechat)
- **language:** jazyk základní verze webu (prázdné = angličtina), zkratka podle http://www.w3.org/WAI/ER/IG/ert/iso639.htm

#### Výstup
- **cached:** vypíná/zapíná keš, silně doporučeno nechat zapnuto
- **xhtml:** přepíná mezi XHTML výstupem (true) a HTML 4.0 (false)
- **allowRobots:** politika vůči vyhledávačům (false jim zakáže přístup na web)
- **author:** autor webu do hlavičky (nedůležité)
- **copyright:** copyright webu do hlavičky (nedůležité)
- **keywords:** klíčová slova webu pro vyhledávače, oddelená čárkou (důležité pro vyhledávače, pomocí pluginu lze měnit i na každé stránce zvlášť)
- **description:** popis webu jednou/dvěma větami (důležité pro vyhledávače, pomocí pluginu lze měnit i na každé stránce zvlášť)

### Krok 3
Po nastavení je možné nechat web zobrazit. Vše by se mělo automaticky nastavit.
Je možné, že Joss napíše, aby se změnila zápisová práva pro některé adresáře.
To lze dobře pomocí `chmod`.

### Automaticky...
Automaticky při prvním zobrazení si Joss vytvoří dle nastavení správně vyplněný soubor `robots.txt`, hlavní `.htaccess` a `sitemap.xml`.

## Struktura

### Složky a soubory
Upozorňění: Všechny soubory jsou v UTF-8. Joss nedává na výběr.

### cache
Tento adresář je pro ukládání souborů s cache (keš). Díky ní lze stránky poskytovat mnohem rychleji. Pro rozjetí Jossu by měl být zapisovatelný.

### class
Složka s jádrem Jossu. Zde jsou všechny PHP třídy, které program využívá.
Obsahuje také třídu `texy.php`, což je (pokud možno) poslední verze Texy!
formátovače.

### class/plugin
Složka s pluginy Jossu. Každá třída zde je potomkem třídy `JPlugin` a definuje
nějaké rozšíření syntaxe Texy!. V úvodním komentáři některých složitějších
rozšíření může být vysvětleno pořadí a typ předávaných paramterů. Pokud si
napíšeš vlastní modul, stačí vycházet z nějakého existujícího, trochu
prostudovat původní třídu `JPlugin` a výsledek svého snažení uložit do tohoto
adresáře. Třída `JPTřída` pak bude automaticky použitelná jako `{{třída: p1, ...}}`.

### config
Složka s konfigurací. Zde je jeden velmi důležitý soubor -- `config.xml`. Je to XML soubor. V tomto souboru jsou všechna potřebná nastavení pro vytvoření webu s Jossem. Více o konfiguraci Jossu je v odstavcích o instalaci.

Soubor `navigation.xml` definuje strukturu navigace. Struktura tohoto XML dokumentu je patrná z příkladu v distribuci. Pokud položka (`item`) nemá definován atribut `url`, vytvoří adresu automaticky z názvu (tedy _Žlutý kůň_ převede na _zluty-kun_).

Dále jsou ve složce šablony (v podsložce `tpl`). Slouží k editaci podoby např. generovaného .htaccess souboru, chybové stránky nebo hlavičky stránky (`<head>`).

### doc
Adresář s dokumentací, licencí apod.

### web/content
Nejdůležitější složka pro webmastera. Nachází se zde zdrojové soubory stránek
v Texy! syntaxi. Obsahuje 2 nutné podsložky `foot`, `head`. Vlastní stránky jsou pak přímo ve složce `content`.

Když se tvoří HTML stránka webu, poskládá se z hlavičky, obsahu a patičky. Běžně
jsou hlavička a patička neměnné a podle zadané adresy se pouze mění vlastní
obsah stránky. Tak to dělá i Joss -- skládá stránku z výchozí hlavičky
`head/_main.texy`, textu podle toho co se nachází v adrese a výchozí patičky
`foot/_main.texy`. Texty čerpá z adresáře `content` a zobrazuje vždy ten odpovídající
adrese -- například `www.example.com/krasny-letni-den` zavolá
`krasny-letni-den.texy`. Pokud v adrese nic není, zavolá se soubor
`index.texy`.

Navíc existují speciální soubory pro chyby -- jedná se o `_404.texy`, `_403.texy` a
`_500.texy` pro odpovídající chybové kódy HTTP (např. 404 -- stránka nenalezena).
Tyto soubory se také nacházejí v adresáři s texty.

Pokud chceš nastavit pro jednu stránku jinou hlavičku (resp. patičku) než pro
zbytek webu, jednoduše do patřičného adresáře pridáš soubor s názvem stránky,
které patří. Např. budu mít v `head/_main.texy` hlavičku pro celý web, ale na
úvodní stránce budu chtít hlavičku jinou, speciální, tak přidám soubor
`head/index.texy` a Joss se s tím popere.

Aby toho nebylo málo, Joss umí pracovat i s takzvanými linky. Link je soubor
pojmenovaný `nazev-stranky.link` a jediné co obsahuje je text s adresou na jiný
zdrojový soubor v Texy!. Může být relativní i absolutní -- lze takto vložit do
svého webu `.texy` soubor třeba přes adresu
`http://www.example.com/stranka.texy`. Linky lze použít pro všechny druhy
zdrojových souborů -- hlavičky, patičky i texty.

Speciální složkou v content může být adresář nazvaný `rss`. Do něj patří linky
na soubory s RSS. Joss tento adresář automaticky prohledá a pokud v něm najde
odkazy na RSS, připojí je ke každé stránce webu. Odkazů může být klidně více
a název souboru zde nehraje jinou roli než rozlišovací. Vytvořím-li např.
`feed.link` a vložím do něj řádek `http://www.example.com/feed/rss.xml`, připojí
se automaticky k webu. Nutno podotknout, že zpracovávání linků na RSS je velmi
náročné a bez cachování nepoužitelné.

### web/content/lang
Složka pro jazykové verze. Nemusí být přítomna, pokud mají stránky jen jednu jazykovou verzi.

Jestliže Joss ve složce `web/content/lang` najde další složky pojmenované podle mezinárodně platných dvoupísmenných zkratek jazyků (cs, en, de, ...), začne automaticky sám přizpůsobovat své chování. Každá z těchto složek by měla přitom obsahovat verzi obsahu analogicky ke složce `web/content` -- např. `web/content/lang/cs/index.texy`, `web/content/lang/cs/foot` apod.

Framework se pak chová tak, že interní adresy vyžaduje ve tvaru `jazyk/stránka`. Místo odkazu `kontakty` je pak tedy třeba v Texy! použít např. `cs/kontakty`. Verze stránek, která není ve speciální složce, ale přímo ve `web/content`, je považována za výchozí a její jazyk je určen pomocí nastavení v `config.ini`. Toto nastavení může "přepsat" uživatel nastavením svého prohlížeče, takže bude-li preferovat jiný jazyk, které stránky nabízí, zobrazí se jako výchozí tato verze.

### web/file
Složka se soubory k webu. Jedná se např. o obrázky použité v obsahu dokumentu, dokumenty PDF nebo věci, které se např. nabízejí ke stažení. Soubory `download.php` a `random.php`
jsou využívány pro možnost vynutit stažení souboru (i když je to např. obrázek)
nebo zobrazit náhodný obrázek. Více o možném použití je v sekci o rozšíření
syntaxe.

### web/css, web/js, web/img, ...
Adresáře se soubory týkajícími se vzhledu webu. Ve složce `css` jsou vyhledávány
CSS soubory, v `js` JavaScript. Do `img` a jemu podobných by se měly ukládat obrázky aj. soubory související se vzhledem webu.

CSS pro celý web je `_main.css`, pokud ale Joss najde soubor odpovídající názvem požadované stránce, vloží ten namísto `_main`. Pokud bys chtěl udělat pouze rozšíření hlavního stylu, využij funkce CSS include a hlavní styl na začátku vlož do speciálního.

Pokud je nalezen styl s příponou `.ie.css` místo `.css`, je vložen s podmíněným
komentářem pro Internet Explorer za běžný styl. Pro název IE stylu jinak platí
stejná pravidla.

Navíc, styly jež nesou název `_media.css`, kde media je jeden ze způsobů použití
stylu vzhledem k zobrazovacímu zařízení podle specifikace CSS, je vložen do
každé stránky webu s patřičnými podmínkami. Např. `_print.css` bude vložen do
každé stránky jako styl pro tisk.

Do složek `web/složka` je vhodné dávat soubory související s designem webu. Jejich uložení či pojmenování nemá kromě složek js a css žádné speciální regule -- soubory jsou využity ve tvém CSS a záleží na tobě, jak je pojmenuješ a jak uspořádáš (např. složku `web/img` můžeš přejmenovat na web/pictures a tak ji použít i ve svém CSS).

## Syntaxe

### Texy!
Veškerá základní syntaxe se řídí [Texy2!][texy-syntax].

### Speciální konstanty
Joss dokáže zpracovat dvě speciální konstanty. Zpracovává je v preprocesoru, takže je jejich vyhodnocení provedeno ještě dříve, než ke slovu přijde Texy! a ostatní formátovače.  Joss zde dbá na dodržení velikosti písmen.

- `$$ ROOT $$` je zaměněno za URL kořenového adresáře webu.
- `$$ LANGUAGE $$` představuje zkratku aktuálního jazyka.

V 98% případů se při tvorbě webu **obejdeš bez těchto konstant**.

### Odkazy
Odkaz například `"takovýto":index` vede automaticky na stránku `index.texy` tvého webu. Odkaz na `"soubor":file:obrazek.png` vytvoří odkaz na `obrazek.png` ve složce
`file`. Odkaz `"obrazek":download:obrazek.png` navíc vynutí jeho stažení místo zobrazení.

### Pluginy
Pluginy jsou rozšířením syntaxe. Zapisují se v Texy! jako `{{název: parametry, parametry}}` nebo bez argumentů jako `{{název}}`. Každý plugin je reprezentován třídou ve složce `class/plugin` (nebo i v jiné, ale předpokládá se tato). Každý plugin musí dědit z třídy `JPlugin`. **Doporučuji při používání pluginů výrazně studovat jejich kód a komentáře tříd.**

#### Eval
Vyhodnotí výraz v PHP a vrátí jeho hodnotu. Dostane-li v parametru např. `2000-5`, vrátí číslo `1995`.

#### Glossary
Něco na způsob mapy webu. Vypíše seznam všech stránek na webu, v abecedním pořadí podle jejich URL. Seznam je vypsán jako HTML seznam `<dir>`.

#### Head
S minimálními parametry vypíše kompletní hlavičku HTML dokumentu. Vše si najde sám a zjistí sám. Parametry nejsou povinné, výchozí se berou z `config.ini`. Jejich nastavením však lze měnit hlavičku podle potřeb aktuální stránky oproti celému zbytku webu. Vykreslí vše od DOCTYPE po uzavírací tag `</head>`. Pokud najde v kořenovém adresáři webu soubor `favicon.ico`, připojí ho také. Více informací v povídání o složce `web`.

#### Include
Následuje předaný odkaz na Texy! soubor, na který směřuje, vloží a zpracuje na
místo, kde je volán. Bez cachování může být tato operace docela náročná. Určitě
není dobré vkládat mnoho stránek kaskádovitě do sebe.

#### Menu
Vytvoří seznam odkazů na základě `navigation.xml`. Jako předaný parametr bere stupeň menu, takže s 0 vykreslí menu nejvyšší úrovně, s 1 první submenu apod. Aktivní stránky jsou místo odkazem tvořeny tagem `<strong>` a mají `class="active"`.

#### Language
Vytvoří jednu položku do menu na přepínání jazyků.

#### Server
Poskytuje hodnoty z proměnné `$_SERVER`.

#### Title
Vloží nadpis právě zobrazované stránky (lze samozřejmě použít např. v hlavičkách a patičkách, které běžně o aktuální stránce nemají potuchy).

#### ČSFD
Příklad konkrétně zaměřeného pluginu -- stahuje vaše poslední hodnocení z [ČSFD.cz][csfd] a vytváří seznam naposledy hodnocených filmů.

#### Config
Tímto pluginem lze měnit základní parametry webu jen pro konkrétní stránku. Lze využít např. pro specifické meta-description a meta-keywords tagy pro každou stránku webu.

#### Google Analytics
Ukázka pluginu zjednodušujícího vložení JavaScriptového kódu měřidla návštěvnosti [Google Analytics][ga].

## Zbytek

### Verze
Např. _Joss 1.0.1_ :-)

- **první číslo** - velké změny
- **druhé číslo** - přidaná funkčnost apod., dílčí změny
- **třetí číslo** - opravy chyb

### Poznámky

- Logo inspirováno http://flickr.com/photos/nesster/1175495215/
- může se hodit... http://www.fortysomething.ca/mt/etc/archives/007253.php (image resizer)
- může se hodit... http://kahi.cz/blog/markitup-texy-set (tlačítka k Texy!)

### Nápady
- jednoduché, ale účinné AJAX administrační rozhraní, které bude heslováno


[gpl]: http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
[ga]: http://www.google.com/analytics/
[csfd]: http://www.csfd.cz
[texy-syntax]: http://texy.info/cs/syntax-podrobne
[joss-intro]: http://blog.javorek.net/ultralight-mezi-php-frameworky-joss/