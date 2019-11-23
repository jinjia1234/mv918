<?php

// ��ȡ������vkey
function getSongVkey($songmid) {
    const $url = 'https://c.y.qq.com/base/fcgi-bin/fcg_music_express_mobile3.fcg';
    const $data = Object.assign({},{
        callback: 'musicJsonCallback',
        loginUin: 3051522991,
        format: 'jsonp',
        platform: 'yqq',
        needNewCode: 0,
        cid: 205361747,
        uin: 3051522991,
        guid: 5931742855,
        songmid: $songmid,
        filename: "C400${songmid}.m4a"
    });
    return jsonp(url, data);
}

//���� res.data.list ����,ֻ����Ҫ��
_formatSongs(list){
  let result = []
  list.forEach((item)=>{
    // console.log('item',item)
    // �⹹��ֵ-�õ�item �µ� musicData �б�����
    let {musicData} = item
    //------------- ���µļ���vkey
    getSongVkey(musicData.songmid).then((res) => {
      const vkey = res.data.items[0].vkey;
      if (musicData.songid && musicData.albummid) {
        result.push(CreatSong(musicData, vkey))
      }
    })
    //-------------
    // console.log('musicData',musicData)
    // if(musicData.songid && musicData.albummid){
    //   result.push(CreatSong(musicData))
    // }
  })
  return result
}

function CreatSong(musicData,vkey){ //����һ�����κ͸�����url
    return new Song({
        id: musicData.songid,
        mid: musicData.songmid,
        singer: filterSinger(musicData.singer),//filterSinger �д���һ��
        name: musicData.songname,
        album: musicData.albumname,
        duration: musicData.interval,
        image: `https://y.gtimg.cn/music/photo_new/T002R300x300M000${musicData.albummid}.jpg?max_age=2592000`,
      ��url: `http://dl.stream.qqmusic.qq.com/C400${musicData.songmid}.m4a?fromtag=38&guid=5931742855&vkey=${vkey}`
    })
}