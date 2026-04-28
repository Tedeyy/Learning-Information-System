function selectOption(selectedOptionId, optionLetter, correctAnswer) {
    // Disable all options so user can't change their answer
    const options = document.querySelectorAll('.flashcard-option');
    options.forEach(opt => {
        opt.classList.remove('interactive');
        opt.classList.add('disabled');
    });
    
    const selectedElement = document.getElementById(selectedOptionId);
    
    // Evaluate answer and apply styles
    if (optionLetter.toUpperCase() === correctAnswer.toUpperCase()) {
        selectedElement.classList.add('option-correct');
    } else {
        selectedElement.classList.add('option-wrong');
        // Highlight the correct one as well so they know
        const correctId = 'option-' + correctAnswer.toUpperCase();
        const correctEl = document.getElementById(correctId);
        if (correctEl) correctEl.classList.add('option-correct');
    }
    
    // Show explanation
    document.getElementById('answer').style.display = 'block';
    
    // Set hidden form value and reveal "Next" / "Finish" button
    document.getElementById('selected_answer').value = optionLetter;
    document.getElementById('next-btn-container').style.display = 'block';
}
