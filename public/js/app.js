$(document).ready(function() {
    $('[data-toggle="tooltip"]').tooltip();
});

function sendAjaxRequest(method, url, data, handler) {
    let request = new XMLHttpRequest();

    request.open(method, url, true);
    request.setRequestHeader(
        "X-CSRF-TOKEN",
        document.querySelector('meta[name="csrf-token"]').content
    );
    request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    request.addEventListener("load", handler);
    request.send(encodeForAjax(data));
}

function encodeForAjax(data) {
    if (data == null) return null;
    return Object.keys(data)
        .map(function(k) {
            return encodeURIComponent(k) + "=" + encodeURIComponent(data[k]);
        })
        .join("&");
}

async function makeFetch(resource, init = {}) {
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    if (!init.headers) init.headers = { "X-CSRF-TOKEN": csrf };
    else init.headers["X-CSRF-TOKEN"] = csrf;
    init.credentials = "same-origin";
    return await fetch(resource, init);
}

function updateVotes(state, upVoteButton, downVoteButton) {
    if (state.vote === undefined) {
        upVoteButton.classList.remove("text-success");
        upVoteButton.classList.add("text-muted");
        downVoteButton.classList.remove("text-info");
        downVoteButton.classList.add("text-muted");
    } else if (state.vote == "true") {
        upVoteButton.classList.add("text-success");
        upVoteButton.classList.remove("text-muted");
        downVoteButton.classList.remove("text-info");
        downVoteButton.classList.add("text-muted");
    } else {
        upVoteButton.classList.remove("text-success");
        upVoteButton.classList.add("text-muted");
        downVoteButton.classList.add("text-info");
        downVoteButton.classList.remove("text-muted");
    }
}

function renderNewStoryForm(state, form) {
    const submit = form.querySelector("#submit");
    const topic2 = form.querySelector("#topic2");
    const topic2Warning = form.querySelector("#topic2-warning");
    const topic3 = form.querySelector("#topic3");
    const topic3Warning = form.querySelector("#topic3-warning");
    if (state.ok) {
        submit.removeAttribute("disabled");
        const topics = form.querySelector("#topics");
        if (topic2Warning) topics.removeChild(topic2Warning);
        if (topic3Warning) topics.removeChild(topic3Warning);
    } else {
        if (state.topic2) {
            if (topic2Warning) topic2Warning.textContent = state.topic2;
            else {
                const warning = document.createElement("p");
                warning.classList.add("text-danger", "text-small", "mb-0");
                warning.id = "topic2-warning";
                warning.textContent = state.topic2;
                topic2.after(warning);
            }
        } else if (topic2Warning) topics.removeChild(topic2Warning);

        if (state.topic3) {
            if (topic3Warning) topic3Warning.textContent = state.topic3;
            else {
                const warning = document.createElement("p");
                warning.classList.add("text-danger", "text-small", "mb-0");
                warning.id = "topic3-warning";
                warning.textContent = state.topic3;
                topic3.after(warning);
            }
        } else if (topic3Warning) topics.removeChild(topic3Warning);

        submit.setAttribute("disabled", "true");
    }
}

function initNewStoryForm(form) {
    const state = { ok: true };
    renderNewStoryForm(state, form);
    const topic1 = form.querySelector("#topic1");
    const topic2 = form.querySelector("#topic2");
    const topic3 = form.querySelector("#topic3");
    const checkTopics = () => {
        let ok = true;
        if (topic2.value.length > 0) {
            if (topic2.value == topic1.value) {
                ok = false;
                state.topic2 =
                    "The topic is the same as topic 1, all topics should be different.";
            } else if (topic1.value.length == 0) {
                ok = false;
                state.topic2 =
                    "You should fill out topic 1 before filling in this topic";
            } else {
                state.topic2 = undefined;
            }
        } else state.topic2 = undefined;

        if (topic3.value.length > 0) {
            if (topic3.value == topic2.value) {
                ok = false;
                state.topic3 =
                    "The topic is the same as topic 2, all topics should be different.";
            } else if (topic2.value.length == 0) {
                ok = false;
                state.topic3 =
                    "You should fill out topic 2 before filling in this topic";
            } else {
                state.topic3 = undefined;
            }
            if (topic3.value == topic1.value) {
                ok = false;
                state.topic3 =
                    "The topic is the same as topic 1, all topics should be different.";
            } else if (topic1.value.length == 0) {
                ok = false;
                state.topic3 =
                    "You should fill out topic 1 before filling in this topic";
            } else {
                state.topic3 = state.topic3 ? state.topic3 : undefined;
            }
        } else state.topic3 = undefined;

        state.ok = ok;
        renderNewStoryForm(state, form);
    };
    topic1.addEventListener("input", checkTopics);
    topic2.addEventListener("input", checkTopics);
    topic3.addEventListener("input", checkTopics);
    checkTopics();
}

function isValidUrl(string) {
    try {
        new URL(string);
    } catch (_) {
        return false;
    }

    return true;
}

async function initVotingSection(section) {
    const storyId = section.dataset.storyId;
    const upVoteButton = section.querySelector(".upvote");
    const rating = section.querySelector(".rating");
    const downVoteButton = section.querySelector(".downvote");
    const state = {};

    const response = await makeFetch(`/api/stories/${storyId}/rate`, {
        method: "GET",
    });
    if (response.status == 200) {
        const vote = await response.json();
        state.vote = vote.vote;
        updateVotes(state, upVoteButton, downVoteButton);
    }

    upVoteButton.addEventListener("click", async() => {
        if (state.vote != "true") {
            const request = await makeFetch(`/api/stories/${storyId}/rate`, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ rating: true }),
            });
            if (request.status == 200) {
                const answer = await request.json();
                rating.textContent = answer.rating;
                state.vote = "true";
                updateVotes(state, upVoteButton, downVoteButton);
            }
        } else {
            const request = await makeFetch(`/api/stories/${storyId}/rate`, {
                method: "DELETE",
                headers: { "Content-Type": "application/json" },
            });
            if (request.status == 200) {
                const answer = await request.json();
                rating.textContent = answer.rating;
                state.vote = undefined;
                updateVotes(state, upVoteButton, downVoteButton);
            }
        }
    });

    downVoteButton.addEventListener("click", async() => {
        if (state.vote != "false") {
            const request = await makeFetch(`/api/stories/${storyId}/rate`, {
                method: "PUT",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ rating: false }),
            });
            if (request.status == 200) {
                const answer = await request.json();
                rating.textContent = answer.rating;
                state.vote = "false";
                updateVotes(state, upVoteButton, downVoteButton);
            }
        } else {
            const request = await makeFetch(`/api/stories/${storyId}/rate`, {
                method: "DELETE",
                headers: { "Content-Type": "application/json" },
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
    document.querySelectorAll(".voting-section").forEach(initVotingSection);
    document.querySelectorAll("#new-story-form").forEach(initNewStoryForm);
}

function commentHandler() {
    if (this.status == 200) window.location.reload();
    else if (this.status == 401) alert("Please sign in to comment!");
    else alert("Could not create comment successfully. Try again later!");
}

function commentStory(story_id) {
    const content = document.getElementById("writeComment").value;
    if (content) {
        let request = { story_id: story_id, content: content };
        sendAjaxRequest("put", "/api/comment", request, commentHandler);
    } else {
        alert("Comment should not be empty");
    }
}

addHandlers();