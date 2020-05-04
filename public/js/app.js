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
                    headers: {
                        'Content-Type': 'application/json',
                        Cookie: 'XSRF-TOKEN='+csrfToken
                    },
                    body: JSON.stringify({
                        'story_id': storyId,
                        rating: true
                    })
                });
                console.log(request);
                const answer = await request.json();
                rating.textContent = answer.rating;
                upVoteButton.classList.remove('text-muted');
                upVoteButton.classList.add('text-success');
            })
        })
}

addHandlers();