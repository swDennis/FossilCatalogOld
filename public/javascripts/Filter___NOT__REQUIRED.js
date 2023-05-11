// var Filter = function() {
//     this.searchTerm = document.querySelector(this.searchTermSelector);
//     this.categories = document.querySelector(this.categoriesSelector);
//     this.tags = document.querySelector(this.tagsSelector);
//
//     this.registerEvents();
// };
//
// Filter.prototype.searchTermSelector = 'input[name="searchTerm"]';
// Filter.prototype.categoriesSelector = 'select[name="categories[]"]';
// Filter.prototype.tagsSelector = 'select[name="tags[]"]';
//
// Filter.prototype.registerEvents = function() {
//     this.categories.addEventListener('change', this.onFormChange.bind(this));
//     this.tags.addEventListener('change', this.onFormChange.bind(this));
//     this.searchTerm.addEventListener('keydown', this.onTypeSearchTerm.bind(this));
// };
//
// Filter.prototype.onFormChange = function() {
//     console.log(this.getFormValues());
// };
//
// Filter.prototype.onTypeSearchTerm = function () {
//     if (this.timeout)  {
//         clearTimeout(this.timeout);
//     }
//
//     this.timeout = setTimeout(() => {
//         this.onFormChange();
//     }, 800);
// };
//
// Filter.prototype.getFormValues = function() {
//     return {
//         'searchTerm': this.searchTerm.value,
//         'tags': [...this.tags.selectedOptions].map(option => option.value),
//         'categories': [...this.categories.selectedOptions].map(option => option.value),
//     };
// };
//
// document.addEventListener("DOMContentLoaded", function() {
//     new Filter();
// });