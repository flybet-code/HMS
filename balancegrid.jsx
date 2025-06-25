// Create Guides Around Selected Object
// This script adds guides at a specified distance around the selected object in Adobe Illustrator.

// Ensure a document is open
if (app.documents.length > 0) {
    var doc = app.activeDocument;

    // Ensure an object is selected
    if (doc.selection.length > 0) {
        var selectedItem = doc.selection[0];
        var offset = 10; // Distance from the object's bounds in points

        // Get the bounds of the selected item
        var bounds = selectedItem.visibleBounds;
        var left = bounds[0] - offset;
        var top = bounds[1] + offset;
        var right = bounds[2] + offset;
        var bottom = bounds[3] - offset;

        // Create a new layer for guides
        var guideLayer = doc.layers.add();
        guideLayer.name = "Guides";

        // Function to create a guide
        function createGuide(x1, y1, x2, y2) {
            var line = doc.pathItems.add();
            line.setEntirePath([[x1, y1], [x2, y2]]);
            line.strokeWidth = 0;
            line.guides = true;
            line.move(guideLayer, ElementPlacement.PLACEATBEGINNING);
        }

        // Create guides around the object
        createGuide(left, top, left, bottom); // Left guide
        createGuide(right, top, right, bottom); // Right guide
        createGuide(left, top, right, top); // Top guide
        createGuide(left, bottom, right, bottom); // Bottom guide

        alert("Guides created around the selected object.");
    } else {
        alert("Please select an object to create guides around.");
    }
} else {
    alert("Please open a document and select an object to create guides.");
}
