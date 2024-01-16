import moment from 'moment';
//moment.tz.setDefault('Asia/Singapore')

export const formatedDate = (date) => {
    return moment(date).format('YYYY-MM-DD HH:mm:ss');
}

export const extractDate = (date) => {
    return moment(date).format('YYYY-MM-DD');
}

export const extractTime = (date) => {
    return moment(date).format('HH:mm:ss');
}

export const currentTime = () => {
    return moment().format('YYYY-MM-DD HH:mm:ss a z');
}

export const extractJobType = (mbjn) => {
    return mbjn.substring(0, 3);
}
export const extractJobNumber = (mbjn) => {
    return mbjn.substring(3);
}
