/*
Spoiler plugin for TinyMCE 4/5 editor

It adds special markup that in combination with a site-side JS script
can create spoiler effect (hidden text that is shown on clik) on a web-page.
An example of a site-side script: https://jsfiddle.net/romanvm/7w9shc27/

(c) 2016, Roman Miroshnychenko <romanvm@yandex.ua>
License: LGPL <http://www.gnu.org/licenses/lgpl-3.0.en.html>
*/
tinymce.PluginManager.add('spoiler', function(editor, url)
{
  var $ = editor.$;
  editor.contentCSS.push(url + '/css/spoiler.css');
  var spoilerCaption = editor.getParam('spoiler_caption', 'Спойлер:');

  if (tinymce.majorVersion == 5)
  {
    editor.ui.registry.addIcon('addspoiler', '<svg width="24" height="24" viewBox="0 0 32 32"><path xmlns="http://www.w3.org/2000/svg" d="M 0.43573203,28.896012 C 0.06046595,28.288819 0.90264662,26.642808 2.3072446,25.23821 L 4.8610592,22.684395 2.3380073,19.376504 -0.18504463,16.068612 1.9144573,13.120134 C 4.7888413,9.0834336 11.925665,5.7694928 16.961573,6.1330968 20.367506,6.3790129 21.49685,5.9107672 24.486417,3.0131654 27.248535,0.33601531 28.265102,-0.13489771 29.220266,0.82026552 30.175429,1.7754288 27.439896,5.0391259 16.481716,16.018319 8.8065091,23.708244 2.2098232,30 1.8224141,30 1.435005,30 0.81099811,29.503206 0.43573203,28.896012 Z M 8.3875,15.1875 C 7.6605038,11.552519 6.5350309,11.131769 5.0351838,13.934259 3.6917202,16.444543 3.7075479,16.564691 5.6357143,18.492857 7.8473413,20.704484 9.1725396,19.112698 8.3875,15.1875 Z m 7.022663,-4.33286 C 14.535061,9.4386964 10.978144,9.893381 10.361626,11.5 c -0.316582,0.825 -0.290715,2.242408 0.05748,3.149797 0.55768,1.453292 0.924078,1.376424 3.076168,-0.64536 1.343695,-1.262336 2.205394,-2.679745 1.914886,-3.149797 z M 11.028025,25.038482 C 9.1568902,23.855157 10.482411,23.350556 17.131306,22.715076 21.700354,22.278382 26.403382,19.755463 27.453914,17.177558 28.060915,15.688035 26.376941,12 25.089816,12 c -0.461899,0 -1.098968,1.295759 -1.415709,2.879464 -0.696166,3.480833 -2.956492,5.902322 -6.290604,6.739131 -4.464453,1.120506 -3.895752,-0.617668 2.148866,-6.567776 6.429265,-6.3287413 7.632071,-6.501183 10.870652,-1.558484 1.602989,2.446471 1.584948,2.71582 -0.365363,5.454779 -3.789427,5.321759 -14.698874,8.817537 -19.009633,6.091368 z"/></svg>');
    editor.ui.registry.addIcon('removespoiler', '<svg width="24" height="24" viewBox="0 0 32 32"><path xmlns="http://www.w3.org/2000/svg" d="M 8.2402449,23.889882 C 5.8973796,22.730185 3.0542372,20.48054 1.9221507,18.890671 L -0.13618842,16 1.9388854,13.085828 C 5.7147388,7.7831304 18,3.5860564 18,7.5987902 c 0,0.8793346 1.357441,2.8668298 3.016535,4.4166558 2.682976,2.506275 2.889786,3.09605 1.870281,5.333621 -1.618402,3.552005 -6.057002,5.237294 -9.757096,3.704665 C 10.346887,19.901045 8,16.423626 8,13.452947 8,11.350247 6.24703,11.669903 5,14 c -2.9566133,5.524482 8.321939,11.166254 16.1849,8.096031 3.35339,-1.309386 7.033039,-5.085707 6.446043,-6.615394 -0.718554,-1.872523 1.99363,-2.637925 3.389136,-0.956443 C 33.883964,17.974965 23.332535,26.00091 15.934276,25.999211 14.045424,25.998778 10.58311,25.04958 8.2402449,23.889882 Z M 15.5,13 c 0,-1.832728 -0.601738,-2.585447 -2.254471,-2.820137 -1.352279,-0.1920247 -2.495586,0.308199 -2.856989,1.25 -0.9956682,2.594671 0.399883,4.739185 2.856989,4.390274 C 14.898262,15.585447 15.5,14.832728 15.5,13 Z M 24,10 C 24,8.6666667 23.333333,8 22,8 20.666667,8 20,7.3333333 20,6 c 0,-1.3333333 0.666667,-2 2,-2 1.333333,0 2,-0.6666667 2,-2 0,-1.33333333 0.666667,-2 2,-2 1.333333,0 2,0.66666667 2,2 0,1.3333333 0.666667,2 2,2 1.333333,0 2,0.6666667 2,2 0,1.3333333 -0.666667,2 -2,2 -1.333333,0 -2,0.6666667 -2,2 0,1.333333 -0.666667,2 -2,2 -1.333333,0 -2,-0.666667 -2,-2 z"/></svg>');
  }

  function addSpoiler()
  {
    var selection = editor.selection;
    var node = selection.getNode();
    if (node) {
      editor.undoManager.transact(function() {
      var content = selection.getContent();
      if (!content) {
        content = 'Скрытый контент';
      }
      selection.setContent('<div class="spoiler">' +
                           '<div class="spoiler-toggle">' + spoilerCaption + ' </div>' +
                           '<div class="spoiler-text">' + content + '</div></div>');
      });
      editor.nodeChanged();
    }
  }

  function removeSpoiler()
  {
    var selection = editor.selection;
    var node = selection.getNode();
    if (node && node.className == 'spoiler')
    {
      editor.undoManager.transact(function()
      {
        var newPara = document.createElement('p');
        newPara.innerHTML = node.getElementsByClassName('spoiler-text')[0].innerHTML;
        node.parentNode.replaceChild(newPara, node);
      });
      editor.nodeChanged();
    }
  }

  editor.on('PreProcess', function(e) {
    $('div[class*="spoiler"]', e.node).each(function(index, elem) {
      if (elem.hasAttribute('contenteditable')) {
        elem.removeAttribute('contentEditable');
      }
    });
  });

  editor.on('SetContent', function() {
    $('div[class*="spoiler"]').each(function(index, elem) {
      if (!elem.hasAttribute('contenteditable')) {
        var $elem = $(elem);
        if ($elem.hasClass('spoiler')) {
          elem.contentEditable = false;
        }
        else if ($elem.hasClass('spoiler-text') || $elem.hasClass('spoiler-toggle')) {
          elem.contentEditable = true;
        }
      }
    });
  });

  if (tinymce.majorVersion == 4)
  {
    editor.addButton('spoiler-add',
    {
      tooltip: 'Add spoiler',
      image: url + '/img/eye-blocked.png',
      onclick: addSpoiler
    });
   editor.addMenuItem('spoiler-add',
    {
      text: 'Add spoiler',
      context: 'format',
      onclick: addSpoiler
    });
    editor.addButton('spoiler-remove',
    {
      tooltip: 'Remove spoiler',
      image: url + '/img/eye-plus.png',
      onclick: removeSpoiler
    });
    editor.addMenuItem('spoiler-remove',
    {
      text: 'Remove spoiler',
      context: 'format',
      onclick: removeSpoiler
    });
  }
  else
  {
    editor.ui.registry.addButton('spoiler-add',
    {
      tooltip: 'Add spoiler',
      icon: 'addspoiler',
      onAction: addSpoiler
    });
   editor.ui.registry.addMenuItem('spoiler-add',
    {
      text: 'Add spoiler',
      context: 'format',
      onAction: addSpoiler
    });
    editor.ui.registry.addButton('spoiler-remove',
    {
      tooltip: 'Remove spoiler',
      icon: 'removespoiler',
      onAction: removeSpoiler
    });
    editor.ui.registry.addMenuItem('spoiler-remove',
    {
      text: 'Remove spoiler',
      context: 'format',
      onAction: removeSpoiler
    });
  }
});
