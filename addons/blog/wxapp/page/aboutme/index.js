var app = getApp();
Page({

  data: {
    my: {}
  },

  onLoad: function (options) {
    var that = this;
    //这里读取关闭我们信息
    app.request('/page/aboutme', function (data) {
      that.setData({ my: data.my});
    });
  },
})