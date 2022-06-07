
<script type="text/javascript">

  // enable form switches

  $(".form-switch").bootstrapSwitch();

  // handle form switch state change

  $(".form-switch").on("switchChange.bootstrapSwitch", function(event, state){

      this.value = state == true ? 1 : 0;
  });

  // enable record update switches

  $(".record-update-switch").bootstrapSwitch();

  // handle record update switch

  $(".record-update-switch").on("switchChange.bootstrapSwitch", function(event, state){

       this.value = state == true ? 1 : 0;

       fieldValueChangeHandler.call(this);
   });

</script>
