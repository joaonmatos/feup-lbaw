$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});

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

async function makeFetch(resource, init = {}) {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    if (!init.headers)
        init.headers = { 'X-CSRF-TOKEN': csrf };
    else
        init.headers['X-CSRF-TOKEN'] = csrf;
    init.credentials = 'same-origin';
    return await fetch(resource, init);
}

function updateVotes(state, upVoteButton, downVoteButton) {
    if (state.vote === undefined) {
        upVoteButton.classList.remove('text-success');
        upVoteButton.classList.add('text-muted');
        downVoteButton.classList.remove('text-info');
        downVoteButton.classList.add('text-muted');
    } else if (state.vote == 'true') {
        upVoteButton.classList.add('text-success');
        upVoteButton.classList.remove('text-muted');
        downVoteButton.classList.remove('text-info');
        downVoteButton.classList.add('text-muted');
    } else {
        upVoteButton.classList.remove('text-success');
        upVoteButton.classList.add('text-muted');
        downVoteButton.classList.add('text-info');
        downVoteButton.classList.remove('text-muted');
    }
}

async function initVotingSection(section) {
    const storyId = section.dataset.storyId;
    const upVoteButton = section.querySelector('.upvote');
    const rating = section.querySelector('.rating');
    const downVoteButton = section.querySelector('.downvote');
    const state = {};

    const response = await makeFetch(`/api/stories/${storyId}/rate`, { method: 'GET' });
    console.log(response);
    if (response.status == 200) {
        console.log('123');
        const vote = await response.json();
        state.vote = vote.vote;
        updateVotes(state, upVoteButton, downVoteButton);
    };

    upVoteButton.addEventListener('click', async() => {
        if (state.vote != 'true') {
            const request = await makeFetch(`/api/stories/${storyId}/rate`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ rating: true })
            });
            if (request.status == 200) {
                const answer = await request.json();
                rating.textContent = answer.rating;
                state.vote = 'true';
                updateVotes(state, upVoteButton, downVoteButton);
            }
        } else {
            const request = await makeFetch(`/api/stories/${storyId}/rate`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' }
            });
            if (request.status == 200) {
                const answer = await request.json();
                rating.textContent = answer.rating;
                state.vote = undefined;
                updateVotes(state, upVoteButton, downVoteButton);
            }
        }
    });

    downVoteButton.addEventListener('click', async() => {
        if (state.vote != 'false') {
            console.log('clicked downvote with no downvote');
            const request = await makeFetch(`/api/stories/${storyId}/rate`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ rating: false })
            });
            console.log(request);
            if (request.status == 200) {
                const answer = await request.json();
                rating.textContent = answer.rating;
                state.vote = 'false';
                updateVotes(state, upVoteButton, downVoteButton);
            }
        } else {
            const request = await makeFetch(`/api/stories/${storyId}/rate`, {
                method: 'DELETE',
                headers: { 'Content-Type': 'application/json' }
            });
            if (request.status == 200) {
                const answer = await request.json();
                rating.textContent = answer.rating;
                state.vote = undefined;
                updateVotes(state, upVoteButton, downVoteButton);
            }
        }
    });

}


function addHandlers() {
    document.querySelectorAll('.voting-section').forEach(section => initVotingSection(section));
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