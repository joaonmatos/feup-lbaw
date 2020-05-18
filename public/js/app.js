function sendAjaxRequest(method, url, data, handler) {
    let request = new XMLHttpRequest();

    request.open(method, url, true);
    request.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    request.addEventListener('load', handler);
    request.send(encodeForAjax(data));
}

function encodeForAjax(data) {
    if (data == null) return null;
    return Object.keys(data).map(function(k) {
        return encodeURIComponent(k) + '=' + encodeURIComponent(data[k])
    }).join('&');
}


function addHandlers() {
    const readCookie = name => {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for (var i = 0; i < ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0) == ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    };

    const csrfToken = readCookie('XSRF-TOKEN');
    document.querySelectorAll('.voting-section')
        .forEach((section) => {
            const storyId = section.dataset.storyId;
            const upVoteButton = section.querySelector('.upvote');
            const rating = section.querySelector('span');
            const downVoteButton = section.querySelector('.downvote');
            upVoteButton.addEventListener('click', async () => {
                const request = await fetch('/api/vote', {
                    method: 'PUT',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        'story_id': storyId,
                        rating: true
                    })
                });
                if (request.status == 200) {
                    const answer = await request.json();
                    rating.textContent = answer.rating;
                    upVoteButton.classList.remove('text-muted');
                    upVoteButton.classList.add('text-success');
                }
            });
            downVoteButton.addEventListener('click', async () => {
                const request = await fetch('/api/vote', {
                    method: 'PUT',
                    credentials: 'same-origin',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        'story_id': storyId,
                        rating: true
                    })
                });
                if (request.status == 200) {
                    const answer = await request.json();
                    rating.textContent = answer.rating;
                    downVoteButton.classList.remove('text-muted');
                    downVoteButton.classList.add('text-success');
                }
            });
        });
}

function commentHandler() {
    if (this.status == 200) {
        let response = JSON.parse(this.responseText);
        window.location.reload();

    }
}

function commentStory(story_id) {

    let content = document.getElementById('writeComment').value;
    console.log("content: " + content);
    if (content) {
        let request = { 'story_id': story_id, 'content': content };
        sendAjaxRequest('put', '/api/comment', request, commentHandler);
    } else {
        alert("Comment should not be empty");
    }
}

addHandlers();