$(document).ready(function () {
  var i = 1
  $('#add').click(function () {
    i++
    $('#dynamic_field').append(
      '<tr id="row' +
        i +
        '"> <td><input type = "number" placeholder="código" onChange = "setPrice(this.value,' +
        i +
        ')" id="id_products' +
        i +
        '" name="products[]" class="form-control"</td> <td>  <input id="price' +
        i +
        '" type="number"class="form-control" name="price[]" value="" readonly /> </td> <td> <input id="quantity' +
        i +
        '" type="number" class="form-control" min="1" max="15" name="quantity[]" value="{{quantity}}" required placeholder="Quantidade"/></td> <td> <input id="discount[]" type="number" class="form-control" name="discount[]" value="{{discount}}" placeholder="Desconto" required /> </td> <td><button type="button" name="remove" id="' +
        i +
        '" class="btn btn-danger btn_remove">X</button></td></tr>'
    )
  })

  $(document).on('click', '.btn_remove', function () {
    var button_id = $(this).attr('id')
    $('#row' + button_id + '').remove()
  })
})




function setPrice(product_id, i) {
  if (i == 0) {
    price_id = 'price'
    quantity_id = 'quantity'
  } else {
    price_id = 'price' + i
    quantity_id = 'quantity' + i
  }

  for (var j = 0; j < Object.keys(products).length; j++) {
    if (products[j][0] == product_id) {
      document.getElementById(price_id).value = products[j][1]
      document.getElementById(quantity_id).max = products[j][2]
    }
  }
}
