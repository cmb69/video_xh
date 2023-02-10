# Video_XH

Video_XH ermöglicht die Präsentation von Videos auf einer
CMSimple_XH-Website. Es verwendet wo immer möglich das HTML5 Video-Element,
und bietet andernfalls einen Downloadlink als Fallback. Die unterstützten
Video-Formate hängen vom Browser ab.

Für jedes Video wird zusätzliches [schema.org](https://schema.org/)
konformes Markup generiert, so dass Suchmaschinen die Videos indexieren
und in Suchergebnissen anzeigen können.

- [Voraussetzungen](#voraussetzungen)
- [Download](#download)
- [Installation](#installation)
- [Einstellungen](#einstellungen)
- [Verwendung](#verwendung)
  - [Beispiele](#beispiele)
  - [Untertitel](#untertitel)
- [Einschränkungen](#einschränkungen)
- [Problembehebung](#problembehebung)
- [Lizenz](#lizenz)
- [Danksagung](#danksagung)

## Voraussetzungen

Video_XH benötigt CMSimple_XH ≥ 1.7.0 und PHP ≥ 7.1.0.

## Download

Das [aktuelle Release](https://github.com/cmb69/video_xh/releases/latest)
kann von Github herunter geladen werden.

## Installation

The Installation erfolgt wie bei vielen anderen CMSimple_XH-Plugins auch.
Im [CMSimple_XH-Wiki](https://wiki.cmsimple-xh.org/de/?fuer-anwender/arbeiten-mit-dem-cms/plugins)
finden Sie weitere Informationen.

1. Sichern Sie die Daten auf Ihrem Server.
1. Entpacken Sie das herunter geladene Archiv auf Ihrem Computer.
1. Laden Sie das komplette Verzeichnis `video/` auf Ihren Server in
   das CMSimple_XH-Pluginverzeichnis hoch.
1. Machen Sie die Unterverzeichnisse `config/`, `css/`
   und `languages/` beschreibbar.
1. Browsen Sie zu `Plugins` → `Video` im Administrationsbereich,
   um zu prüfen, ob alle Voraussetzungen erfüllt sind.

## Einstellungen

Die Plugin-Konfiguration wird wie bei vielen anderen CMSimple_XH-Plugins
auch im Administrationsbereich durchgeführt.
Gehen Sie zu `Plugins` → `Video`.

Sie können die Voreinstellungen von Video_XH unter `Konfiguration` ändern.
Hinweise zu den Optionen werden angezeigt, wenn Sie die Hilfe-Icons
mit der Maus überfahren.

Die Lokalisierung wird unter `Sprache` vorgenommen. Sie können dort die
Sprachtexte in Ihre eigene Sprache übersetzen (falls keine entsprechende
Sprachdatei zur Verfügung steht), oder diese gemäß Ihren Wünschen anpassen.

Das Aussehen von Video_XH kann unter `Stylesheet` angepasst werden.

## Verwendung

Da Video_XH keinen Upload anbietet, müssen Sie Ihre Video-Dateien in den
eingestellten Order per FTP, Filebrowser oder
[Uploader_XH](https://github.com/cmb69/uploader_xh) hoch laden.
Es werden die folgenden Video-Formate unterstützt: MP4 (AVC
Baseline@L3.0/AAC), WebM (VP8/Vorbis) und OGG (Theora/Vorbis).
Sie können Ihre Videos mit [XMedia Recode](https://www.xmedia-recode.de/)
(verwenden Sie das `HTML 5` Profil) leicht in diese Formate konvertieren.
Um alle bedeutenden Browser zu unterstützen sollten Sie eine
`*.mp4` *und* eine `*.webm`/`*.ogv` Version hoch laden.
Zusätzlich können Sie ein so genanntes Poster in den gleichen Ordner hoch laden,
also ein Bild im JPEG-Format (`*.jpg`), das angezeigt wird bevor das Video abspielt.

Um ein Video auf einer CMSimple_XH-Seite anzuzeigen, fügen Sie dort den
folgenden Plugin-Aufruf ein:

    {{{video('%NAME%', '%OPTIONEN%')}}}

Die Bedeutung der Platzhalter:

- `%NAME%`:
  Der Name der Video-Datei *ohne Datei-Erweiterung*. Um genau zu sein,
  ist dies ein Dateipfad relativ zum eingestellten Video-Ordner.

- `%OPTIONEN%`:
  Optionen für dieses Video als Zeichenkette im selben Format wie ein
  Query-String (alles hinter dem Fragezeichen in einem HTTP-GET-Request).
  Optionen, die nicht angegeben werden, verwenden die entsprechenden Werte aus
  der Plugin-Konfiguration.
  Sie können den `Aufruf-Assitenten`, der in der Plugin-Administration,
  und optional in einem Reiter oberhalb des Editors, verfügbar ist,
  verwenden, um den Aufruf zusammen zu stellen.
  Nachdem Sie mit der Eingabe der gewünschten Werte fertig sind, kopieren Sie
  einfach den Inhalt der Textarea ganz unten und fügen ihn auf einer Seite ein.
  Wird ein bereits bestehender Pluginaufruf in diese Textarea eingefügt,
  wird der Pluginaufruf geparst und die Steuerelemente aktualisiert, was
  leichtes Bearbeiten ermöglicht.

Um ein Video auf allen Seiten anzuzeigen, fügen Sie das folgende ins
Template ein:

    <?=video('%NAME%', '%OPTIONS%')?>

### Beispiele

    {{{video('LotR')}}}

Dies wird das `LotR.webm`, `LotR.mp4` oder `LotR.ogv` Video im konfigurierten
Video-Ordner mit der konfigurieren Breite und Höhe anzeigen.
Wenn `LotR.jpg` existiert, wird es als Poster verwendet.

    {{{video('music/thriller', 'width=320&height=240')}}}

Wird das `thriller` Video aus dem Unterordner `music/`
mit einer Größe von 320px × 240px anzeigen.
Beachten Sie, dass das Skalieren beim Playback nicht unbedingt die beste Option ist.

    {{video('banner', 'autoplay&loop&controls=0')}}}

Wird automatisch das `banner` Video in einer Schleife abspielen,
ohne Steuerelemente anzuzeigen.
Das ist besonders nützlich für Banner- oder Hintergrundvideos,
kann aber Ihre Besucher nerven.

### Untertitel

In unterstützenden Browsern können die Video-Player optional Untertitel
anzeigen.
Die Untertitel müssen sich in separaten Dateien befinden (nicht in
den Container gemuxt), wobei
[WebVTT](https://developer.mozilla.org/en-US/docs/Web/API/WebVTT_API)
das empfohlene Datei-Format ist, obgleich SRT ebenfalls funktionieren kann.
Legen Sie einfach eine entsprechende Datei `*.vtt` (oder `*.srt`)
neben den Video-Dateien ab, und der Besucher ist in der Lage die Untertitel
zu aktivieren, wenn er das möchte.

Für mehrsprachige Websites können Sie Untertitel in den entsprechenden
Sprachen nutzen.
Benennen Sie sie einfach `*_LANG.vtt` (oder `*_LANG.srt`,
z.B. also `video_en.vtt` (oder `video_de.vtt`).
Der Player wird nur diejenigen Untertitel der aktuell gewählten Sprache anbieten.

## Einschränkungen

Der Aufruf-Assistent ist nur in zeitgemäßen Browsern verfügabr;
beispielsweise wird IE 9 nicht unterstützt.

## Problembehebung

Falls das Video *gar* nicht abgespielt werden kann, könnte es daran
liegen, dass das Video mit einem falschen MIME-Typ ausgeliefert wird.
Sollte dies der Fall sein, dann können Sie einen Apache-WebServer vermutlich
mit den folgenden Zeilen in `.htaccess` entsprechend konfigurieren:

    AddType video/webm .webm
    AddType video/mp4 .mp4
    AddType video/ogg .ogv

Wenn dies nicht möglich ist, bitten Sie Ihren Hosting-Provider oder
Server-Administrator die nötige Konfiguration vorzunehmen.

Wenn das Video nicht *sofort* abgespielt werden kann, liegt das
vermutlich an einem Kodierungs- oder Multiplex-Fehler.
Es scheint, dass viele MP4-Muxer das `moov` Atom ans Ende des Videos hängen,
was den Download der gesamten Datei erforderlich macht
bevor die Wiedergabe starten kann.
Das Beste ist zu versuchen, das Video mit
[Yamb](http://yamb.unite-video.com/) (für MP4) oder
[MKVToolNix](https://mkvtoolnix.download/) (für WebM)
erneut zu multiplexen.

Melden Sie Programmfehler und stellen Sie Supportanfragen entweder auf
[Github](https://github.com/cmb69/video_xh/issues)
oder im [CMSimple_XH Forum](https://cmsimpleforum.com/).

## Lizenz

Video_XH ist freie Software. Sie können es unter den Bedingungen
der GNU General Public License, wie von der Free Software Foundation
veröffentlicht, weitergeben und/oder modifizieren, entweder gemäß
Version 3 der Lizenz oder (nach Ihrer Option) jeder späteren Version.

Die Veröffentlichung von Video_XH erfolgt in der Hoffnung, daß es
Ihnen von Nutzen sein wird, aber *ohne irgendeine Garantie*, sogar ohne
die implizite Garantie der *Marktreife* oder der *Verwendbarkeit für einen
bestimmten Zweck*. Details finden Sie in der GNU General Public License.

Sie sollten ein Exemplar der GNU General Public License zusammen mit
Video_XH erhalten haben. Falls nicht, siehe <https://www.gnu.org/licenses/>.

© 2012-2023 Christoph M. Becker

Dänische Übersetzung © 2012 Jens Maegard  
Tschechische Übersetzung © 2012 Josef Němec  
Slovakische Übersetzung © 2012 Dr. Martin Sereday

## Danksagung

Das Plugin-Icon wurde von [Alessandro Rei](http://www.mentalrey.it/) entworfen.
Vielen Dank für die Veröffentlichung unter GPL.

Vielen Dank an die Community im [CMSimple_XH-Forum](https://www.cmsimpleforum.com/)
für Tipps, Vorschläge und das Testen.
Besonders möchte ich *bca* danken, da er der erste Beta-Tester war,
die Skins *tube* und *tube2* beigesteuert hat
und mich über Video.js 4 informiert hat,
sowie *Ulrich* und *Holger*, die die Unterstützung von Untertiteln angeregt haben.

Und zu guter letzt vielen Dank an [Peter Harteg](http://www.harteg.dk/),
den „Vater“ von CMSimple, und allen Entwicklern von
[CMSimple_XH](https://www.cmsimple-xh.org/de/) ohne die es dieses
phantastische CMS nicht gäbe.
