function PluginThemeVersion(){
  this.data = {row: null, application: null};
  this.row_click = function(){
    PluginWfBootstrapjs.modal({id: 'modal_version', content: '', label: 'Version'});
    PluginWfDom.render([{type: 'div', innerHTML: [{type: 'strong', innerHTML: 'Application'}, {type: 'div', innerHTML: this.data.application.title}]}], 'modal_version_body');
    PluginWfDom.render([{type: 'div', innerHTML: [{type: 'strong', innerHTML: 'Host'}, {type: 'div', innerHTML: this.data.application.host}]}], 'modal_version_body');
    PluginWfDom.render([{type: 'div', innerHTML: [{type: 'strong', innerHTML: 'Date'}, {type: 'div', innerHTML: this.data.row[0]}]}], 'modal_version_body');
    PluginWfDom.render([{type: 'div', innerHTML: [{type: 'strong', innerHTML: 'Version'}, {type: 'div', innerHTML: this.data.row[1]}]}], 'modal_version_body');
    PluginWfDom.render([{type: 'div', innerHTML: [{type: 'strong', innerHTML: 'Title'}, {type: 'div', innerHTML: this.data.row[2]}]}], 'modal_version_body');
    PluginWfDom.render([{type: 'div', innerHTML: [{type: 'strong', innerHTML: 'Description'}, {type: 'div', innerHTML: this.data.row[3]}]}], 'modal_version_body');
    PluginWfDom.render([{type: 'p', innerHTML: [{type: 'a', innerHTML: 'Send mail', attribute: {class: 'btn btn-secondary', onclick: 'PluginThemeVersion.send_mail()'}}]}], 'modal_version_body');
  }
  this.send_mail = function(){
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
    window.location.href='mailto:?subject='+subject+'&body='+body;
  }
}
var PluginThemeVersion = new PluginThemeVersion();
