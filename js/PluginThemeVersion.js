function PluginThemeVersion(){
  this.data = {row: null, application: null, tester: []};
  this.row_click = function(){
    PluginWfBootstrapjs.modal({id: 'modal_version_row', content: '', label: 'Version'});
    PluginWfDom.render([{type: 'div', innerHTML: [{type: 'strong', innerHTML: 'Application'}, {type: 'div', innerHTML: this.data.application.title}]}], 'modal_version_row_body');
    PluginWfDom.render([{type: 'div', innerHTML: [{type: 'strong', innerHTML: 'Host'}, {type: 'div', innerHTML: this.data.application.host}]}], 'modal_version_row_body');
    PluginWfDom.render([{type: 'div', innerHTML: [{type: 'strong', innerHTML: 'Date'}, {type: 'div', innerHTML: this.data.row[0]}]}], 'modal_version_row_body');
    PluginWfDom.render([{type: 'div', innerHTML: [{type: 'strong', innerHTML: 'Version'}, {type: 'div', innerHTML: this.data.row[1]}]}], 'modal_version_row_body');
    PluginWfDom.render([{type: 'div', innerHTML: [{type: 'strong', innerHTML: 'Title'}, {type: 'div', innerHTML: this.data.row[2]}]}], 'modal_version_row_body');
    PluginWfDom.render([{type: 'div', innerHTML: [{type: 'strong', innerHTML: 'Description'}, {type: 'div', innerHTML: this.data.row[3]}]}], 'modal_version_row_body');
    PluginWfDom.render([{type: 'div', innerHTML: [{type: 'strong', innerHTML: 'Working'}, {type: 'div', innerHTML: this.data.row[4]}]}], 'modal_version_row_body');
    PluginWfDom.render([{type: 'div', innerHTML: [{type: 'strong', innerHTML: 'Issue'}, {type: 'div', innerHTML: this.data.row[5]}]}], 'modal_version_row_body');
    PluginWfDom.render([{type: 'div', innerHTML: [
      {type: 'a', innerHTML: 'Send mail', attribute: {class: 'btn btn-secondary', onclick: 'PluginThemeVersion.send_mail()'}},
      {type: 'a', innerHTML: 'Response', attribute: {class: 'btn btn-primary', onclick: 'PluginThemeVersion.response()'}}
    ], attribute: {style: 'margin-top:40px'}}], 'modal_version_row_body');
  }
  this.response = function(){
    PluginWfBootstrapjs.modal({id: 'modal_version_response', content: null, label: 'Response', size: 'sm'});
    PluginWfDom.render([{type: 'ul', innerHTML: [
      {type: 'a', innerHTML: 'Do not understand', attribute: {href: '#', onclick: 'PluginThemeVersion.response_click(this)', data_value: 'Do not understand', class: 'list-group-item list-group-item-action'}}, 
      {type: 'a', innerHTML: 'Working', attribute: {href: '#', onclick: 'PluginThemeVersion.response_click(this)', data_value: 'Working', class: 'list-group-item list-group-item-action'}}, 
      {type: 'a', innerHTML: 'Not working', attribute: {href: '#', onclick: 'PluginThemeVersion.response_click(this)', data_value: 'Not working', class: 'list-group-item list-group-item-action'}}, 
      {type: 'a', innerHTML: 'Could not test', attribute: {href: '#', onclick: 'PluginThemeVersion.response_click(this)', data_value: 'Could not test', class: 'list-group-item list-group-item-action'}},
      {type: 'a', innerHTML: '(no response)', attribute: {href: '#', onclick: 'PluginThemeVersion.response_click(this)', data_value: '', class: 'list-group-item list-group-item-action'}}
    ], attribute: {class: 'list-group'}}
    ], 'modal_version_response_body');
  }
  this.response_click = function(btn){
    $.get( "/theme_version/response?version="+this.data.row[1]+"&response="+btn.getAttribute('data_value'), function( data ) {
      $('#modal_version_response').modal('hide');
      $('#modal_version_row').modal('hide');
    });    
  }
  this.send_mail = function(){
    var mailto = '';
    for(i=0; i<this.data.tester.length; i++){
      mailto += ';'+this.data.tester[i]['account.email'];
    }
    mailto = mailto.substr(1);
    var subject = 'Version '+this.data.row[1];
    if(this.data.application.title){
      subject += ' - '+this.data.application.title;
    }
    var description = this.data.row[3];
    description = description.replace(/(<([^>]+)>)/gi, "");
    var body = '';
    body += 'Application: '+this.data.application.title+'%0D%0A';
    body += 'Host: '+this.data.application.host+'%0D%0A';
    body += 'Date: '+this.data.row[0]+'%0D%0A';
    body += 'Version: '+this.data.row[1]+'%0D%0A';
    body += 'Title: '+this.data.row[2]+'%0D%0A';
    body += 'Description:%0D%0A '+description+'%0D%0A';
    window.location.href='mailto:'+mailto+'?subject='+subject+'&body='+body;
  }
}
var PluginThemeVersion = new PluginThemeVersion();
