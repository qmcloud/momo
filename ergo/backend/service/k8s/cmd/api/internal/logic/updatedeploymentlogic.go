package logic

import (
	"backend/common/errorx"
	"backend/service/k8s/cmd/api/internal/svc"
	"backend/service/k8s/cmd/api/internal/types"
	"context"
	"github.com/zeromicro/go-zero/core/logx"
	metav1 "k8s.io/apimachinery/pkg/apis/meta/v1"
	"k8s.io/client-go/kubernetes"
	"k8s.io/client-go/tools/clientcmd"
	"log"
)

type UpdateDeploymentLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewUpdateDeploymentLogic(ctx context.Context, svcCtx *svc.ServiceContext) UpdateDeploymentLogic {
	return UpdateDeploymentLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *UpdateDeploymentLogic) UpdateDeployment(req types.RequestCreateDeployment) (*types.Response, error) {
	configPath := "k8s/kube.config"

	config, err := clientcmd.BuildConfigFromFlags("", configPath)
	if err != nil {
		log.Fatal(err)
	}
	clientSet, err := kubernetes.NewForConfig(config)
	if err != nil {
		log.Fatal(err)
	}
	getDeployment, err := clientSet.AppsV1().Deployments(req.Namespace).Get(context.TODO(), req.Name, metav1.GetOptions{})
	getDeployment.Spec.Replicas = &req.Replicas
	getDeployment.Spec.Template.Spec.Containers[0].Image = req.Image

	update, err := clientSet.AppsV1().Deployments(req.Namespace).Update(context.TODO(), getDeployment, metav1.UpdateOptions{})
	if err != nil {
		return nil, errorx.NewCodeError(201, "更新失败", err)
	}
	return nil, errorx.NewCodeError(200, "成功", update)
}
