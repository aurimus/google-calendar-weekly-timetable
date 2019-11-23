// window.addEventListener('load', function() {
//   var timetables = document.querySelectorAll('.gcwt.instance')
//   var hook

//   do {
//     hook = timetables[0].parentNode
//   } while ( hook.querySelectorAll('.gcwt.instance').length < timetables.length)

//   var button = document.createElement('button')
//   var buttonName = document.createTextNode("Save Colors")
//   button.appendChild(buttonName)

//   hook.parentNode.insertBefore(button, hook)

//   button.addEventListener('click', function(){
//     var eventNodes = hook.querySelectorAll('.gcwt-event')
//     var colors = []
//     eventNodes.forEach((eventNode) => {
//       if (eventNode.parentNode.style['background-color']) {
//         colors.push({
//           name: eventNode.textContent.trim(), 
//           color: eventNode.parentNode.style['background-color']
//         })
//       }
//     })
//     fetch(window.ajaxurl + '?action=set_colors', {
//       method: 'POST',
//       body: JSON.stringify(colors),
//       headers: {'Content-Type': 'application/json'},
//     }).then(function(response) { return response.json() })
//       .then(function(json) {
//         alert('Colors updated')
//       })
//   })
// })