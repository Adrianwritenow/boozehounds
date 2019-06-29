(function($, window, document) {

  // $ is now locally scoped and available

  $(function() {
    
    $(document).ready(function() {
      
      let currentValues = undefined

      // vaccination record on click

      // edit button on click
      $('.admin-edit-dog').on('click', function(e) {
        e.preventDefault()
        let tableRow = $(e.currentTarget).closest('tr'),
            rowData = tableRow.find('td'),
            dogId = tableRow.data('dogId'),
            editUrl = tableRow.data('editUrl')
        if($('#tr-edit-form')) {
          $('#tr-edit-form').remove()
        } 
        
        currentValues = {
          name: $(rowData[2]).find('p').text(),
          gender: $(rowData[3]).find('p').text(),
          breed: $(rowData[4]).find('p').text(),
          vacc_expiration: $(rowData[5]).find('p').text()
        }
        
        tableRow.after(edit_dog_form(dogId, editUrl))
        
      })

      $('.dog-table').on('click', '[name=save]', function(e){
        e.preventDefault()
        let dogId = $(e.currentTarget).closest('tr').data('dogId'),
            url = $(e.currentTarget).closest('tr').data('editUrl')
  
        saveDogEdit(url, currentValues)
      })

      $('.dog-table').on('click', '[name=cancel]', function(e){
        e.preventDefault()
        cancelDogEdit()
      })
      
      // delete button on click
      $('.admin-remove-dog').on('click', function(e) {
        e.preventDefault()
        let url = e.target.href

        $.ajax({
          url: url,
          type: "GET"
        }).then(res => {
          window.location.reload() 
        })
      })

      function saveDogEdit(url) {
        let dogForm = $('.dog-table').find('#edit-dog-form'),
            photo = $('.dog-table').find('[name=photo]').prop('files'),
            name = $('.dog-table').find('[name=name]').val(),
            gender = $('.dog-table').find('[name=gender]').val(),
            breed = $('.dog-table').find('[name=breed]').val(),
            vacc_expiration = $('.dog-table').find('[name=vacc_expiration]').val(),
            vaccination = $('.dog-table').find('[name=vaccination]').prop('files'),
            formData = new FormData()

        photo.length > 0 ? formData.append('photo', photo[0]) : null
        name.length > 0 ? formData.append('name', name) : null
        currentValues.gender !== gender ? formData.append('gender', gender) : null
        breed.length > 0 ? formData.append('breed', breed) : null
        vacc_expiration.length > 0 ? formData.append('vacc_expiration', vacc_expiration) : null
        vaccination.length > 0 ? formData.append('vaccination', vaccination[0]) : null

        $.ajax({
          url: url,
          method: "POST",
          processData: false,
          contentType: false,
          data: formData
        }).then(res => {
          window.location.reload()
        })        
      }

      function cancelDogEdit() {
        $('#tr-edit-form').remove()
      }

      function edit_dog_form(dogId, editUrl) {
        return `
        <tr data-edit-url="${editUrl}" data-dog-id="${dogId}" id="tr-edit-form" ><form id="edit-dog-form" enctype="multipart/form-data">
          <td>
            <input type="hidden" name="action" value="manage_dog_accounts" />
            <input type="hidden" name="manage_dog_accounts_form" value="add" />
          </td>
          <td>
            <label>
            <span>Photo</span>
            <input type="file" name="photo" />
            </label>
          </td>
          <td>
            <label>
              <span>Name</span>
              <input type="text" name="name" />
            </label>
          </td>
          <td>
            <label>
              <span>Gender</span>
              <select name="gender">
                <option value="male">Male</option>
                <option value="female">Female</option>
              </select>
            </label>
          </td>
          <td>
            <label>
              <span>Breed</span>
              <input id="dog-breed" type="text" name="breed" />
            </label
          </td>
          <td>
            <label>
              <span>Vaccination Expiration</span>
              <input type="date" name="vacc_expiration" />
            </label>
          </td>
          <td>
          <label>
            <span>Vaccination Record</span>
            <input type="file" name="vaccination" />
          </label>
        </td>
          <td>
            <button name="save" value="<?php _e('Submit', 'memberpress-corporate') ?>" >Save</button>
          </td>
          <td>
            <button name="cancel" value="<?php _e('Cancel', 'memberpress-corporate') ?>" >Cancel</button>
          </td>
        </form></tr>`
      }

    });
  })

})(jQuery, window, document)