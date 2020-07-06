var app = getApp();
Page({

  data: {
    archiveList:[]
  },

  onLoad: function (options) {
    var that = this;
    app.request('/archive/index', {}, function (data, ret) {
      that.setData({
        archiveList: data.archiveList
      });
    }, function (data, ret) {
      app.error(ret.msg);
    });
  },
})