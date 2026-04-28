function toggleInputs() {
    const type = document.getElementById('materialType').value;
    document.getElementById('fileGroup').style.display = type === 'file' ? 'block' : 'none';
    document.getElementById('videoGroup').style.display = type === 'video' ? 'block' : 'none';
    
    // Toggle required attributes to prevent form submission errors
    document.querySelector('input[name="video_url"]').required = (type === 'video');
    // File input shouldn't strictly be required via HTML if they want to update, but for creation it is.
    document.querySelector('input[name="material_file"]').required = (type === 'file');
}
// Run on load
document.addEventListener('DOMContentLoaded', toggleInputs);
