function getUserTimeZone() {
    let timeZoneOffset = new Date().getTimezoneOffset();

    let timeZoneOffsetHours = -timeZoneOffset / 60;

    console.log("User's time zone offset: " + timeZoneOffsetHours + " hours");
    return timeZoneOffsetHours;
}
function getAuthToken() {
    const cookieString = document.cookie; // Get all cookies as a single string
    const cookies = cookieString ? cookieString.split("; ") : []; // Split into individual cookies
    for (let cookie of cookies) {
        const [cookieName, cookieValue] = cookie.split("=");
        if (cookieName === "auth_token") {
            return cookieValue; // Return the auth_token value
        }
    }
    return null; // Return null if auth_token is not found
}
