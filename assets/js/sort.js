function handleSortChange() {
    var sortOption = document.getElementById("sort-options").value;
    var url = new URL(window.location.href);
    url.searchParams.set('sort', sortOption);  // Add or update the 'sort' query parameter
    window.location.href = url;  // Reload the page with the new sort parameter
}