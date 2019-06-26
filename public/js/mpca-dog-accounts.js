// IIFE



(function($, window, document) {
  
  // $ is now locally scoped and available
  
  $(function() {
    console.log('This is loading !!');
    
    let dogForm = $('#mpca-add-sub-user-form'),
        formUrl = dogForm.attr('action'),
        checkoutButton = $('#dog-checkout a'),
        dogSubmitButton = dogForm.find('input[type=submit]'),
        uploadsDir = $('meta[name=upload-directory]').attr('content')
        checkoutUrl = checkoutButton.attr('href')

    dogSubmitButton.prop('disabled', false)
    checkoutButton.removeAttr('href')

    console.log(uploadsDir);
    

    dogForm.on('submit', (e) => {
      e.preventDefault()
      let formData = new FormData()

      formData.append('name', dogForm.find('[name=name]').val())
      formData.append('breed', dogForm.find('[name=breed]').val())
      formData.append('gender', dogForm.find('[name=gender]').val())
      formData.append('vacc_expiration', dogForm.find('[name=vacc_expiration]').val())
      formData.append('photo', dogForm.find('[name=photo]').prop('files')[0])
      formData.append('vaccination', dogForm.find('[name=vaccination]').prop('files')[0])
      
      submitDogForm(formUrl, formData)
      
    })
    /**
     * Add Form Data for files
     */

    function submitDogForm(url, data) {
      dogSubmitButton.prop('disabled', true)
      $.ajax({
        url: url,
        type: 'POST',
        data: data,
        cache : false,
        contentType : false,
        processData : false,
        success : function(response){
          response = JSON.parse(response)
          if(response.status == true) {  
            templateDogAccounts(response)
            dogForm.find('[name=name]').val('')
            dogForm.find('[name=breed]').val('')
            dogForm.find('[name=vacc_expiration]').val('')
            dogForm.find('[name=photo]').prop('files')[0] = ''
            dogForm.find('[name=vaccination]').prop('files')[0] = ''
            dogSubmitButton.prop('disabled', false)
          }
      }
      })
    }  

    function templateDogAccounts(response) {
      let { dog_accounts, ca } = response
          dogCount = $('#mpca_sub_accounts_used h4'),
          dogTable = $('#mpca-dog-accounts-table')

      dogCount.text(`${dog_accounts.length} of ${ca.num_sub_accounts} dogs used`)
      dogTable.find('tbody').empty()
      dog_accounts.map(dog => dogTable.find('tbody').append(templateDogRow(dog)))
      if (ca.num_sub_accounts == dog_accounts.length){
        dogForm.remove()
        checkoutButton.attr('href', checkoutUrl)
      } 
    }

    function templateDogRow(dog) {
      return `<tr data-dog-id="${dog.id}" id="mpca-sub-accounts-row>" class="mpca-sub-accounts-row">
        <td><img src="${uploadsDir}/dogs/photos/${dog.photo}" alt="${dog.name}"style="width:80px;height:60px"></td>
        <td>${dog.name}</td>
        <td>${dog.breed}</td>
        <td>${dog.gender}</td>
        <td>${dog.vacc_expiration}</td>
        <td><img src="${uploadsDir}/dogs/vaccinations/${dog.vaccination}" alt="${dog.name}" style="width:80px;height:60px"></td>
        <?php $alt = !$alt; ?>
      </tr>`
    }

  });
}(jQuery, window, document));